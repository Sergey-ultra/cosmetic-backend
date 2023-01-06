<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewVideoRequest;
use App\Models\Sku;
use App\Models\SkuVideo;
use App\Services\VideoSavingService\VideoSavingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class SkuVideoController extends Controller
{
    const VIDEO_FOLDER = 'public/video/sku/';

    public function addOrUpdateVideo(ReviewVideoRequest $request, VideoSavingService $videoSavingService): JsonResponse
    {
        $skuId = $request->sku_id;


        $currentSku = Sku::select('products.code')
            ->join('products', 'products.id', '=', 'skus.product_id')
            ->where('skus.id', $skuId)
            ->first();

        if (!$currentSku) {
            return response()->json([
                'data' => [
                    'status' => 'success',
                    'message' => 'Товарного предложения не существует'
                ]
            ], 404);
        }


        $videoFilePath = $videoSavingService->saveOneFile($request->file, self::VIDEO_FOLDER, $currentSku->code);


        $skuVideo = SkuVideo::updateOrCreate(
            [
                'sku_id' => $skuId,
                'user_id' => Auth::guard('sanctum')->user()->id
            ],
            [
                'video' => $videoFilePath,
                'description' => $request->description
            ]
        );


        return response()->json(['data' =>
            [
                'status'=> 'success',
                'data' => $skuVideo
            ]
        ], 201);
    }
}
