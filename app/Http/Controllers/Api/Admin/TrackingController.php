<?php

declare(strict_types=1);


namespace App\Http\Controllers\Api\Admin;


use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\DataProviderWithDTO;
use App\Http\Controllers\Traits\ParamsDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrackingController extends Controller
{
    use DataProviderWithDTO;

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->per_page ?? 10;
        $query =   DB::table('trackings')
            ->select([
                'trackings.id as id',
                'trackings.email as email',
                'trackings.created_at as created_at',
                'products.name as name',
                'skus.volume as volume',
                'sku_store.price as price'
            ])
            ->join('skus', 'trackings.sku_id', '=', 'skus.id')
            ->leftJoin('sku_store', 'skus.id', '=', 'sku_store.sku_id')
            ->join('products', 'skus.product_id', '=', 'products.id');

        $paramsDto = new ParamsDTO(
            $request->input('filter', []),
            $request->input('sort', ''),
        );

        $result = $this->prepareModel($paramsDto, $query)->paginate($perPage);
        return response()->json($result);
    }

    public function dynamics(): JsonResponse
    {
        $result = DB::table('trackings')
            ->select(DB::raw("count(email) AS count, DATE(created_at) AS date "))
            ->groupBy(DB::raw('WEEK(created_at)'))
            ->orderBy(DB::raw('WEEK(created_at)'))
            ->get();
        return response()->json(['data'=> $result]);
    }
}
