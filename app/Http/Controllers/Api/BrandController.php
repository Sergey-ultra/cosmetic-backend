<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BrandController extends Controller
{
    public function all(): JsonResponse
    {
        $brands = Brand::query()->select('id', 'name')->get();
        return response()->json(['data' => $brands]);
    }

    /**
     * @return JsonResponse
     */
    public function byLetters(): JsonResponse
    {
        $brands = Brand::query()
            ->select(DB::raw('brands.id as id, brands.name as name, brands.code, countries.name as country'))
            ->join('products', 'products.brand_id', '=', 'brands.id')
            ->join('skus', 'skus.product_id', '=', 'products.id')
            ->join('sku_store', 'sku_store.sku_id', '=', 'skus.id')
            ->leftJoin('countries', 'countries.id', '=', 'brands.country_id')
            ->groupBy('id', 'name', 'code', 'country')
            ->orderBy('brands.name', 'ASC')
            ->get();


        $letters = [];
        foreach ($brands as $brand) {
            $currentLetter = mb_strtoupper(mb_substr($brand->name, 0, 1));
            $letterKey = array_search($currentLetter, array_column($letters, 'letter'));

            if ($letterKey === false) {
                $letters[] = [
                    'letter' => $currentLetter,
                    'brands' => [
                        $brand
                    ]
                ];
            } else {
                $letters[$letterKey]['brands'][] = $brand;
            }
        }

        return response()->json(['data' => $letters]);
    }

    public function byCode(string $code): BrandResource
    {
        return new BrandResource(Brand::query()->where('code', $code)->first());
    }


    public function popular(): JsonResponse
    {
        $productsWithSkusQuery = DB::table('products')
            ->selectRaw('count(products.brand_id) AS sku_count, products.brand_id')
            ->join('skus', 'skus.product_id', '=', 'products.id')
            ->groupBy('products.brand_id')
        ;

        $result = DB::table('brands')
            ->select([
                'brands.name',
                'brands.code',
                'brands.image'
            ])
            ->joinSub($productsWithSkusQuery, 'productsWithSkus', function ($join) {
                $join->on( 'productsWithSkus.brand_id', '=', 'brands.id');
            })
            ->orderBy('productsWithSkus.sku_count', 'DESC')
            ->limit(10)
            ->get();

        return response()->json(['data' => $result]);
    }
}
