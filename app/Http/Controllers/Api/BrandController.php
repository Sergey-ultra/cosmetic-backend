<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BrandRequest;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Sku;
use App\Services\Parser\Text;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class BrandController extends Controller
{
    /**
     * @return JsonResponse
     */
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

    /**
     * @param string $code
     * @return BrandResource
     */
    public function byCode(string $code): BrandResource
    {
        return new BrandResource(Brand::query()->where('code', $code)->first());
    }


    /**
     * @return JsonResponse
     */
    public function popular(): JsonResponse
    {
        $productsWithSkusQuery = DB::table(Product::TABLE)
            ->selectRaw(sprintf('count(%s.brand_id) AS sku_count, %s.brand_id', Product::TABLE, Product::TABLE))
            ->join(
                Sku::TABLE,
                sprintf('%s.product_id', Sku::TABLE),
                '=',
                sprintf('%s.id', Product::TABLE)
            )
            ->groupBy(sprintf('%s.brand_id', Product::TABLE));

        $result = DB::table(Brand::TABLE)
            ->select([
                sprintf('%s.name', Brand::TABLE),
                sprintf('%s.code', Brand::TABLE),
                sprintf('%s.image', Brand::TABLE),
            ])
            ->joinSub($productsWithSkusQuery, 'productsWithSkus', function ($join) {
                $join->on('productsWithSkus.brand_id', '=', sprintf('%s.id', Brand::TABLE));
            })
            ->orderBy('productsWithSkus.sku_count', 'DESC')
            ->limit(10)
            ->get();

        return response()->json(['data' => $result]);
    }

    /**
     * @param BrandRequest $request
     * @return JsonResponse
     */
    public function store(BrandRequest $request): JsonResponse
    {
        $name = $request->input('name');

        $newBrand = Brand::query()->firstOrCreate([
            'name' => $name,
            'code' =>  Text::makeCode($name),
        ], [
            'user_id' => Auth::guard('api')->id(),
        ]);

        return response()->json(['data' => $newBrand], Response::HTTP_CREATED);
    }
}
