<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\SearchSkusResource;
use App\Models\Sku;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function index(SearchRequest $request): JsonResponse
    {
        $result = Sku::query()
            ->select(
                'products.id AS id',
                'products.name AS name',
                DB::raw("CONCAT(products.code,  '-', skus.id) AS sku_code"),
                'skus.id AS sku_id',
                'skus.volume AS volume',
                'skus.images',
                DB::raw("CONCAT('/category/', categories.code) AS category_url"),
                'categories.name AS category_name'
            )
            ->join('products', 'products.id', '=', 'skus.product_id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
//            ->join('sku_store', 'skus.id', '=', 'sku_store.sku_id')
//            ->join('links', 'sku_store.link_id', '=', 'links.id')
//            ->whereNotNull('sku_store.price')
            ->where('products.name', 'LIKE', "%{$request->input('search')}%")
            ->groupBy('products.id', 'products.name', 'products.code', 'skus.id', 'skus.volume','skus.images', 'categories.code', 'categories.name')
            ->limit(8)
            ->get()
        ;

        $categories = [];
        foreach($result->toArray() as $item) {
            if (!in_array($item['category_name'], array_column( $categories, 'name'), true)) {
                $categories[] = [
                    'name' => $item['category_name'],
                    'url' => $item['category_url']
                ];
            }
        }


        return response()->json([
            'data' => [
                'categories' => $categories,
                'skus' => SearchSkusResource::collection($result)
            ]
        ]);
    }
}
