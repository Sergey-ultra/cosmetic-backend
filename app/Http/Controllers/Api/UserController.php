<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserUpdateRequest;
use App\Models\SkuRating;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $user->name = $request['name'];

        $user->save();

        $user->info()->update([
            'sex' => $request['sex'],
            'birthday_year' => $request['birthday_year']
        ]);


        return response()->json(['data' => ['status' => true]]);
    }

}
