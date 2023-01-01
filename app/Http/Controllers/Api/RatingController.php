<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RatingRequest;
use App\Models\Sku;
use App\Models\SkuRating;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RatingController extends Controller
{
    /**
     *
     * @params \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkUserRating(Request $request): JsonResponse
    {
        $skuId = $request->sku_id;
        $visitorIp = request()->ip();
        $user = Auth::guard('sanctum')->user();

        $existingRating = SkuRating::where([
            'sku_id' => $skuId,
            'ip_address' => $visitorIp
        ])->first();

        if (!$existingRating && isset($user)) {
            $existingRating = SkuRating::where([
                'sku_id' => $skuId,
                'user_id' => $user->id
            ])->first();
        }

        if (!$existingRating) {
            return response()->json(['data' => 0]);
        }

        return response()->json(['data'=>
            [
                'status' => 'success',
                'data' => $existingRating->rating
            ]
        ]);
    }

    /**
     *
     * @params \App\Http\Requests\RatingRequest
     * @return \Illuminate\Http\JsonResponse
     */
    public function createOrUpdate(RatingRequest $request): JsonResponse
    {
        $skuId = $request->sku_id;
        $user = Auth::guard('sanctum')->user();
        $visitorIp = request()->ip();


        $existingRating = SkuRating::where([
            'sku_id' => $skuId,
            'ip_address' => $visitorIp
        ])->first();



        if (!$existingRating && isset($user)) {
            $existingRating = SkuRating::where([
                'sku_id' => $skuId,
                'user_id' => $user->id
            ])->first();
        }

        $params['rating'] = $request->rating;

        if (isset($user)) {
            $params['user_id'] = $user->id;
            $params['user_name'] = $user->name;
        }

        $skuRatingCount = SkuRating::where('sku_id', $skuId)->count();
        $currentSku = Sku::find($skuId);

        if ($existingRating) {
            $newCommonRating = ($currentSku->rating * $skuRatingCount + $request->rating - $existingRating->rating) / $skuRatingCount;
            $existingRating->update($params);
        } else {
            $params['sku_id'] = $skuId;
            $params['ip_address'] = $visitorIp;

            SkuRating::create($params);
            $newCommonRating = ($currentSku->rating * $skuRatingCount + $request->rating ) / ($skuRatingCount + 1);
        }

        $currentSku->rating = $newCommonRating;
        $currentSku->save();

        return response()->json(['data' =>
            ['status' => 'success']
        ]);
    }
}
