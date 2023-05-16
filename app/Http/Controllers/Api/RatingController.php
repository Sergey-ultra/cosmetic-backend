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
        $skuId = $request->input('sku_id');
        $visitorIp = request()->ip();
        $user = Auth::guard('api')->user();
        $conditions['sku_id'] = $skuId;

        if (isset($user)) {
            $conditions['user_id']=  $user->id;
        } else {
            $conditions['ip_address'] = $visitorIp;
        }

        $existingRating = SkuRating::query()->where($conditions)->first();

        if (!$existingRating) {
            return response()->json(['data' => 0]);
        }

        return response()->json(['data'=> [
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
        $skuId = $request->input('sku_id');
        $user = Auth::guard('api')->user();
        $visitorIp = request()->ip();


        if (isset($user)) {
            $existingRating = SkuRating::query()->where(['sku_id' => $skuId, 'user_id' => $user->id])->first();
        }
        if (!isset($existingRating)) {
            $existingRating = SkuRating::query()->where(['sku_id' => $skuId, 'ip_address' => $visitorIp])->first();
        }

        $params['rating'] = $request->input('rating');


        $skuRatingCount = SkuRating::where('sku_id', $skuId)->count();
        $currentSku = Sku::find($skuId);


        if ($existingRating) {
            if (isset($user)) {
                $params['user_id'] = $user->id;
                $params['user_name'] = $user->name;
            }

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
