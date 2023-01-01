<?php
declare(strict_types=1);


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class RouteController extends Controller
{
    public function skusQueries(): JsonResponse
    {
        $result = DB::table('products')
            ->selectRaw("CONCAT(products.code,  '-', skus.id) AS route")
            ->join('skus', 'products.id', '=', 'skus.product_id')
            ->get()
            ->pluck('route')
            ->all();

        return response()->json(['data' => $result]);
    }

    public function articles():JsonResponse
    {
        $result = DB::table('articles')
            ->selectRaw("CONCAT('/article/', slug) AS route")
            ->get()
            ->pluck('route')
            ->all();
        return response()->json(['data' =>  $result ]);
    }

    public function categories(): JsonResponse
    {
        $result = DB::table('categories')
            ->selectRaw("CONCAT('/category/', code) AS route")
            ->whereNotNull('parent_id')
            ->get()
            ->pluck('route')
            ->all();
        return response()->json(['data' =>  $result ]);
    }


    public function brands(): JsonResponse
    {
        $result = DB::table('brands')
            ->selectRaw("CONCAT('/brand/', brands.code) AS route")
            ->join('products', 'brands.id', '=','products.brand_id')
            ->groupBy('brands.code')
            ->get()
            ->pluck('route')
            ->all();
        return response()->json(['data' => $result]);
    }
}
