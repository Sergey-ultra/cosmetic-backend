<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sku;
use App\Models\SkuRating;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class RatingController extends Controller
{
    /**
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function dynamic(): JsonResponse
    {
        $result = DB::table('sku_ratings')
            ->select(DB::raw("count(id) AS count, DATE(created_at) AS date "))
            ->where('user_id', '<>', 1)
            ->where('user_name', '<>', 'Robot.Smart-Beautiful')
            ->groupBy(DB::raw('WEEK(created_at)'))
            ->orderBy('date')
            ->get();

        return response()->json(['data'=> $result]);
    }

    /**
     *
     * @return \Illuminate\Http\JsonResponse
     */
//    public function addInitialRatings(): JsonResponse
//    {
//        try {
//            $skuIdsWithoutRating = Sku::distinct()
//                ->select('skus.id as id')
//                ->leftJoin('sku_ratings', 'skus.id', '=', 'sku_ratings.sku_id')
//                ->whereNull('sku_ratings.rating')
//                ->get()
//                ->pluck('id')
//                ->all();
//
//            $insertedRatingsToSku = array_map(function($el) {
//                return [
//                    'id' => $el,
//                    'rating' => 5
//                ];
//            }, $skuIdsWithoutRating);
//
//            $insertedRatingsToSkuRating = array_map(function($el) {
//                return [
//                    'sku_id' => $el,
//                    'user_id' => NULL,
//                    'user_name' => 'Robot.Smart-Beautiful',
//                    'rating' => 5
//                ];
//            }, $skuIdsWithoutRating);
//
//
//            DB::beginTransaction();
//            Sku::upsert($insertedRatingsToSku, []);
//            SkuRating::insert($insertedRatingsToSkuRating);
//            DB::commit();
//
//            return response()->json([
//                'data' =>
//                    [
//                        'status' => true,
//                        'message' => 'Начальные рейтинги добавлены',
//                    ]
//            ]);
//
//        } catch (\Exception $e) {
//
//            DB::rollBack();
//            return response()->json([
//                'data' => [
//                    'status' => true,
//                    'message' => $e->getMessage()
//                ]
//            ], 500);
//        }
//    }
}
