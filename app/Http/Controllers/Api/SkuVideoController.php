<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\DataProvider;
use App\Http\Requests\ReviewVideoRequest;
use App\Http\Resources\MyVideoCollection;
use App\Models\Sku;
use App\Models\SkuVideo;
use App\Services\VideoSavingService\VideoSavingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SkuVideoController extends Controller
{
    use DataProvider;

    const VIDEO_FOLDER = 'public/video/sku/';

    public function my(Request $request)
    {
        $perPage = (int) ($request->per_page ?? 10);

        $query = Sku::select([
            'products.name AS sku_name',
            'products.code AS product_code',
            'skus.id AS sku_id',
            'skus.volume',
            'skus.images',
            'sku_videos.video',
            'sku_videos.thumbnail',
            'sku_videos.status',
            'sku_videos.description',
            'sku_videos.created_at',
            'users.name AS user_name',
            'user_infos.avatar'
        ])
            ->join('sku_videos', 'skus.id', '=', 'sku_videos.sku_id')
            ->join('products', 'skus.product_id', '=', 'products.id')
            ->join('users', 'users.id', '=', 'sku_videos.user_id')
            ->leftjoin('user_infos', 'users.id', '=', 'user_infos.user_id')
            ->where([
                'sku_videos.user_id' => Auth::id(),
                ['sku_videos.status', '<>', 'deleted']
            ])
        ;

        $result = $this->prepareModel($request, $query)->paginate($perPage);

        return new MyVideoCollection($result);
    }

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


        [$videoFilePath, $thumbnailPath] = $videoSavingService->saveOneFile($request->file, self::VIDEO_FOLDER, $currentSku->code);


        $skuVideo = SkuVideo::updateOrCreate(
            [
                'sku_id' => $skuId,
                'user_id' => Auth::guard('sanctum')->user()->id
            ],
            [
                'video' => $videoFilePath,
                'thumbnail' => $thumbnailPath,
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
