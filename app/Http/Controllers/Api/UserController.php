<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserUpdateRequest;
use App\Models\Country;
use App\Models\SkuRating;
use App\Models\UserInfo;
use App\Services\UserLocationService\UserLocationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserController extends Controller
{

    public function me(Request $request): JsonResponse
    {
        $user = Auth::user();
        $info = $user->info;

        $result = [
            'email' => $user->email,
            'role' => $user->role->name,
            'name' => $user->name,
            'avatar' => $info->avatar ?? '/storage/icons/user_avatar.png'
        ];

        if ((bool)$request->is_expand) {
            if (isset($info)) {
                $result['sex'] = $info->sex;
                $result['birthday_year'] = $info->birthday_year;
            }
            $reviewCount = SkuRating::join('reviews', 'reviews.sku_rating_id', '=', 'sku_ratings.id')
                ->where([
                    ['sku_ratings.user_id', $user->id],
                    ['reviews.status', 'published']
                ])
                ->count();
            $result['review_count'] = $reviewCount;
        }

        return response()->json(['data' => $result]);
    }

    public function updateMe(UserUpdateRequest $request): JsonResponse
    {
        $user = Auth::user();
        $user->name = $request->input('name');
        $user->save();


        $user->info()->updateOrCreate([], [
            'sex' => $request->input('sex') ?? null,
            'birthday_year' => $request->input('birthday_year')
        ]);

        return response()->json(['data' => ['status' => true]]);
    }

    public function startNotificationBot(): JsonResponse
    {
        $user = Auth::user();
        if (!$user->telegramInfo) {
            $hash = Str::uuid();
            $user->telegramInfo()->updateOrCreate([],['hash' => $hash]);
        } else {
            $hash = $user->telegramInfo->hash;
        }


        $qrCode = '';
        $botUrl = config('telegrambot.user_notification_url') . '?start=' . $hash;
        try {
            $qrCode = QrCode::size(200)
                ->backgroundColor(255, 255, 0)
                ->color(0, 0, 255)
                ->margin(1)
                ->generate($botUrl);
        } catch (\Throwable $e) {
            $qrCode = null;
        }

        return response()->json([
            'status' => true,
            'data' => [
                'qr_code' => $qrCode,
                'bot_url' => $botUrl
            ]
        ]);
    }

    public function updateTelegramUser(Request $request): void
    {
        $user = Auth::user();
        $user->telegramInfo()->updateOrCreate([],['telegram_user_name' => json_encode($request)]);
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
