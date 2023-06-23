<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AvatarRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\Country;
use App\Models\SkuRating;
use App\Models\UserInfo;
use App\Services\ImageSavingService\ImageSavingService;
use App\Services\UserLocationService\UserLocationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        ];

        if ((bool)$request->input('is_expand')) {
            $result['sex'] = $info?->sex;
            $result['birthday_year'] = $info?->birthday_year;

            $reviewCount = SkuRating::query()
                ->join('reviews', 'reviews.sku_rating_id', '=', 'sku_ratings.id')
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
