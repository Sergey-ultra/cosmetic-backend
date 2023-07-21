<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AvatarRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\Country;
use App\Models\Review;
use App\Models\User;
use App\Models\UserInfo;
use App\Repositories\UserRepository\UserRepository;
use App\Services\EntityStatus;
use App\Services\ImageSavingService\ImageSavingService;
use App\Services\UserLocationService\UserLocationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class UserController extends Controller
{
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
            'refLink' => sprintf('/ref=%s', $user->ref),
            'refBalance' => $user->referralBalanceNormal ?? 0,
            'unviewed_message_count' => $user->messages()->where('is_viewed', false)->count(),
        ];

        if ($request->boolean('is_expand')) {
            $result['sex'] = $info?->sex;
            $result['birthday_year'] = $info?->birthday_year;

            $reviewCount = Review::query()
                ->where(['user_id' => $user->id, 'status' => 'published'])
                ->count();
            $result['review_count'] = $reviewCount;
        }

        return response()->json(['data' => $result]);
    }

    public function myMessages(UserRepository $userRepository): JsonResponse
    {
        $userid = Auth::guard('api')->id();
        $result = $userRepository->getChatsByUserId($userid);

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
