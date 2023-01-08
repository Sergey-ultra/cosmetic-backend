<?php


namespace App\Http\Controllers\Api\Admin;


use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\DataProvider;
use App\Http\Requests\StatusRequest;
use App\Http\Resources\Admin\SkuVideoCollection;
use App\Models\Sku;
use App\Models\SkuVideo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class SkuVideoController extends Controller
{
    use DataProvider;
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \App\Http\Resources\Admin\SkuVideoCollection
     */
    public function index(Request $request): SkuVideoCollection|JsonResponse
    {
        $perPage = (int) ($request->per_page ?? 10);

        if ($perPage === -1) {
            $result = SkuVideo::select('id', 'video')->get();
            return response()->json(['data' => $result]);
        }

        $query = Sku::select([
            'products.name AS name',
            'products.code',
            'skus.id AS sku_id',
            'skus.volume',
            'skus.images',
            'sku_videos.id',
            'sku_videos.video',
            'sku_videos.thumbnail',
            'sku_videos.status',
            'sku_videos.description',
            'sku_videos.created_at',
            'users.name AS user_name'
        ])
            ->join('sku_videos', 'skus.id', '=', 'sku_videos.sku_id')
            ->join('products', 'skus.product_id', '=', 'products.id')
            ->join('users', 'users.id', '=', 'sku_videos.user_id')
            ->leftjoin('user_infos', 'users.id', '=', 'user_infos.user_id')
        ;


        $result = $this->prepareModel($request, $query, true)->paginate($perPage);

       return new SkuVideoCollection($result);
    }

    public function setStatus(int $id, StatusRequest $request): JsonResponse
    {
        SkuVideo::where('id', $id)->update(['status' => $request->status]);
        return response()->json(['data' => ['status' => 'success']]);
    }
}
