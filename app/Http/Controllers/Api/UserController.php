<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AvatarRequest;
use App\Http\Requests\ChargeRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\WalletRequest;
use App\Jobs\UserChargeMoneyJob;
use App\Models\Country;
use App\Models\Review;
use App\Models\User;
use App\Models\UserBalanceCharge;
use App\Models\UserInfo;
use App\Models\UserWallet;
use App\Repositories\MessageRepository\MessageRepository;
use App\Services\EntityStatus;
use App\Services\ImageSavingService\ImageSavingService;
use App\Services\UserLocationService\UserLocationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;


class UserController extends Controller
{
    public const DEFAULT_WALLET_TYPE = 'yoomoney';
    public const IMAGES_FOLDER = 'public/image/avatar/';
    public function me(Request $request): JsonResponse
    {
        $user = Auth::guard('api')->user();
        $info = $user->info;

        $result = [
            'email' => $user->email,
            'role' => $user->role(),
            'name' => $user->name,
            'balance' => $user->balanceNormal ?? 0,
            'avatar' => $info->avatar ??  UserInfo::DEFAULT_AVATAR,
            'ref_link' => sprintf('/ref=%s', $user->ref),
            'ref_balance' => $user->referralBalanceNormal ?? 0,
            'is_first_charge' => $user->moneyCharges()->count() === 0,
            'number_of_invited_authors' => User::query()->where('referral_owner', $user->id)->count(),
            'unviewed_message_count' => $user->messages()->where('is_viewed', false)->count(),
        ];

        if ($request->boolean('is_expand')) {
            $result['sex'] = $info?->sex;
            $result['birthday_year'] = $info?->birthday_year;

            $reviewCount = Review::query()
                ->where(['user_id' => $user->id, 'status' => EntityStatus::PUBLISHED])
                ->count();
            $result['review_count'] = $reviewCount;
        }

        return response()->json(['data' => $result]);
    }



    public function updateMe(UserUpdateRequest $request): JsonResponse
    {
        $user = Auth::guard('api')->user();
        $user->name = $request->input('name');
        $user->save();


        $user->info()->updateOrCreate([], [
            'sex' => $request->input('sex') ?? null,
            'birthday_year' => $request->input('birthday_year')
        ]);

        return response()->json(['data' => ['status' => true]]);
    }

    public function updateAvatar(AvatarRequest $request, ImageSavingService $imageSavingService): JsonResponse
    {
        $user = Auth::guard('api')->user();

        $image = $imageSavingService->saveOneImage($request->input('avatar'), self::IMAGES_FOLDER, $user->name);

        $user->info()->updateOrCreate([], [
            'avatar' => $image,
        ]);

        return response()->json(['data' => ['status' => true]]);
    }

    public function bestUsers(): JsonResponse
    {
        $result = User::query()
            ->select(
                sprintf('%s.id', User::TABLE),
                sprintf('%s.name', User::TABLE),
                sprintf('%s.avatar', UserInfo::TABLE),
                DB::raw(sprintf('count(%s.id) AS review_count', Review::TABLE)),
            )
            ->join(
                UserInfo::TABLE,
                sprintf('%s.user_id', UserInfo::TABLE),
                '=',
                sprintf('%s.id', User::TABLE)
            )
            ->join(
                Review::TABLE,
                sprintf('%s.user_id', Review::TABLE),
                '=',
                sprintf('%s.id', User::TABLE)
            )
            ->where(sprintf('%s.status', Review::TABLE), EntityStatus::PUBLISHED)
            ->groupBy(
                sprintf('%s.id', User::TABLE),
                sprintf('%s.name', User::TABLE),
                sprintf('%s.avatar', UserInfo::TABLE),
            )
            ->orderBy('review_count', 'DESC')
            ->limit(10)
            ->get();

        return response()->json(['data' => $result]);
    }

    public function wallets(): JsonResponse
    {
        $wallets = UserWallet::query()
            ->select('id', 'identifier AS name')
            ->where('user_id', Auth::guard('api')->id())
            ->get();

        return response()->json(['data' => $wallets]);
    }

    public function storeWallet(WalletRequest $request):JsonResponse
    {
        $newWallet = UserWallet::query()->create([
            'identifier' => $request->input('to'),
            'type' => self::DEFAULT_WALLET_TYPE,
            'user_id' => Auth::guard('api')->id(),
        ]);

        return response()->json(['data' => [
            'id' => $newWallet->id,
            'name' => $newWallet->identifier,
            ]
        ], Response::HTTP_CREATED);
    }

    public function getUserCharges(): JsonResponse
    {
        $result = UserBalanceCharge::query()
            ->with('wallet')
            ->where('user_id', Auth::guard('api')->id())
            ->get()
            ->map(function(UserBalanceCharge $item) {
                return [
                    'id' => $item->id,
                    'ordered_amount' => $item->ordered_amount,
                    'amount' => $item->amount,
                    'payment_date' => $item->payment_date,
                    'status' => $item->status,
                    'created' => $item->created_at->format('Y-m-d'),
                    'wallet' => $item->wallet->identifier,
                ];
            })
            ->all();

        return response()->json(['data' => $result]);

    }

    public function charge(ChargeRequest $request): JsonResponse
    {
        $user = Auth::guard('api')->user();

        $amount = $request->input('amount');
        if ($amount > $user->balanceNormal) {
            return response()->json(['message' => 'Сумма превышает баланс'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $walletId = $request->input('wallet_id');
        $wallet = UserWallet::query()->find($walletId);

        if (!$wallet) {
            return response()->json([], Response::HTTP_NOT_FOUND);
        }

        $label = Str::uuid()->toString();
        $newCharge = UserBalanceCharge::query()->create([
            'uuid' => $label,
            'user_id' => $wallet->user_id,
            'wallet_id' => $wallet->id,
            'ordered_amount' => $amount,
            'amount' => $amount,
            'status' => UserBalanceCharge::STATUS_PROCESSED,
        ]);


        $user->balance -= $amount * 1000;
        $user->save();



        $result = [
            'id' => $newCharge->id,
            'ordered_amount' => $newCharge->ordered_amount,
            'amount' => $newCharge->amount,
            'payment_date' => $newCharge->payment_date,
            'status' => $newCharge->status,
            'created' => $newCharge->created_at->format('Y-m-d'),
            'wallet' => $newCharge->wallet->identifier,
        ];
        $to = $wallet->identifier;
        $type = $wallet->type;
        $to = (int)preg_replace('/[^0-9]/', '', $to);

        UserChargeMoneyJob::dispatch($amount, $to, $type, $label);

        return response()->json(['data' => $result], Response::HTTP_CREATED);
    }


    public function getMyLocation(Request $request, UserLocationService $userLocationService): JsonResponse
    {
        $userIp = $request->ip();
        $location = $userLocationService->getLocationByIp($userIp);
        $result = [];

        if (isset($location['country'])) {
            $result = Country::query()
                ->select('id', 'name', 'name_en')
                ->where('name_en', '=', $location['country'])
                ->first();
        }


        return response()->json($result);
    }
}
