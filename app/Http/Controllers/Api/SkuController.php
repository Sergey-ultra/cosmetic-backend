<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ComparedSkuResource;
use App\Http\Resources\ProductResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sku;
use App\Services\SkuService\SkuService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;


class SkuController extends Controller
{
    const SMALL_IMAGES_FOLDER = 'small';

    /**
     * Display a listing of the resource.
     *
     * @params \Illuminate\Http\Request $request
     * @params \App\Services\SkuService $skuService
     * @return \Illuminate\Http\JsonResponse
     */
    public function mainIndex(Request $request, SkuService $skuService): JsonResponse
    {
        $categoryCode = $request->category_code;
        $brandCode = $request->brand_code;


        $brandIds =  $request->brand_ids;
        $categoryIds =  $request->category_ids;
        $activeIngredientsGroupIds =  $request->active_ingredients_group_ids;
        $countryIds =  $request->country_ids;
        $volumes =  $request->volumes;
        $maxPrice =  $request->max_price;
        $minPrice =  $request->min_price;

        $sort = $request->sort;
        $perPage = (int) ($request->per_page ?? 24);
        $page =  $request->page;

        $search = $request->search;

        if ($search) {

        }


        $result = [];

        //$productQuery = Product::with(['skus', 'brand'])->where('category_id' , $category->id);
        //$products = ProductsWithBrandAndSkuResource::collection($productData)->response()->getData();
        //$productData = $productData->paginate($perPage);
        //$products = ProductsWithBrandAndSkuResource::collection($productData)->response()->getData();

        $productQuery = Product::select(
            'products.id as id',
            'products.name as name',
            'products.code as code',
            'products.category_id as category_id',
            'categories.name as category',
            'brands.name as brand',
            'skus.id as sku_id',
            'skus.volume as volume',
            'skus.images as images',
            'skus.rating as rating',
            'sku_store.price as price',
            'links.code as link_code',
            'stores.name as store_name',
            'stores.image as store_image',
            'skus.reviews_count as reviews_count'
        )
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('skus', 'products.id', '=', 'skus.product_id')
            ->join('sku_store', 'skus.id', '=', 'sku_store.sku_id')
            ->join('stores', 'sku_store.store_id', '=', 'stores.id')
            ->join('links', 'sku_store.link_id', '=', 'links.id')
            ->join('brands', 'products.brand_id', '=', 'brands.id');


        if ($activeIngredientsGroupIds) {
            $activeIngredientsQuery = DB::table('active_ingredients_group_ingredient')
                ->select('ingredient_product.product_id AS product_id')
                ->join('ingredient_product', 'ingredient_product.ingredient_id', '=', 'active_ingredients_group_ingredient.ingredient_id')
                ->whereIn('active_ingredients_group_ingredient.active_ingredients_group_id', $activeIngredientsGroupIds)
                ->groupBy('ingredient_product.product_id')
            ;


            $productQuery = $productQuery
                ->joinSub($activeIngredientsQuery,'ingredients', function($join) {
                    $join->on('products.id', '=', 'ingredients.product_id');
                });
        }

        $productQuery = $productQuery->whereNotNull('sku_store.price');


        if ($categoryCode) {

            $category = Category::select(['id', 'name'])->where('code', $categoryCode)->first();
            if (!$category) {
                return response()->json(['error' => 'Not Found'], 404);
            }

            $result["category"] = $category;
            $productQuery = $productQuery->where('products.category_id', $category->id);

        } else if ($brandCode) {

            $brand = Brand::select(['id', 'name'])->where('code', $brandCode)->first();
            if (!$brand) {
                return response()->json(['error' => 'Not Found'], 404);
            }


            $result["brand"] = $brand;
            $productQuery = $productQuery->where('products.brand_id', $brand->id);

        } else if ($search) {
            $productQuery = $productQuery->where('products.name', 'LIKE', "%$search%");
//            $productQuery = $productQuery->whereRaw(
//                "MATCH(products.name, products.name_en, products.description, products.application) AGAINST(?)",
//                [$search]
//            );
        }


        if ($brandIds) {
            $productQuery = $productQuery->whereIn('products.brand_id', $brandIds);
        }
        if ($countryIds) {
            $productQuery = $productQuery->whereIn('brands.country_id', $countryIds);
        }
        if ($categoryIds) {
            $productQuery = $productQuery->whereIn('products.category_id', $categoryIds);
        }
        if ($volumes) {
            $productQuery = $productQuery->whereIn('skus.volume', $volumes);
        }
        if ($minPrice) {
            $productQuery = $productQuery->where('sku_store.price', '>', $minPrice);
        }
        if ($maxPrice) {
            $productQuery = $productQuery->where('sku_store.price', '<', $maxPrice);
        }

        $rawProductData = $productQuery->get()->toArray();



        $skuService->setAllSkus($rawProductData);
        $skuService->setSmallImagesFolder(self::SMALL_IMAGES_FOLDER);

        $productsWithSkus = $skuService
            ->groupAllSkusWithPricesToOneSku()
            ->groupSkusToOneProduct()
            ->sort($sort)
            ->paginate($page, $perPage);

        $result['products'] = $productsWithSkus;

        return response()->json($result);
    }


    /**
     *
     * @params \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function viewed(Request $request): JsonResponse
    {
        $ids = $request->ids;

        $result = Product::select(
            'skus.id as id',
            'products.name as name',
            'products.code as code',
            'brands.name as brand',
            'skus.volume as volume',
            'skus.images as image',
            'skus.rating as rating',
            'skus.reviews_count as reviews_count',
            DB::raw('MIN(sku_store.price) as min_price')
        )
            ->join('skus', 'products.id', '=', 'skus.product_id')
            ->join('sku_store', 'skus.id', '=', 'sku_store.sku_id')
            ->join('links', 'sku_store.link_id', '=', 'links.id')
            ->join('brands', 'products.brand_id', '=', 'brands.id')
            ->whereIn('skus.id', $ids)
            ->groupBy(['id', 'name', 'code', 'brand','volume','image', 'rating', 'reviews_count'])
            ->get()
        ;

        foreach($result as &$sku) {
            $sku['image'] = json_decode($sku['image'], true)[0];
        }


        return response()->json(['data' => $result]);
    }

    /**
     *
     * @params int $skuId
     * @params \Illuminate\Http\Request $request
     * @return \App\Http\Resources\ProductResource | \Illuminate\Http\JsonResponse
     */

    public function bySkuId($skuId, Request $request): ProductResource|JsonResponse
    {
        if ($request->view === 'compact') {
            $currentSku = Sku::with('product')->find($skuId);
            return response()->json([
                'data' => [
                    'id' => $currentSku->id,
                    'name' => $currentSku->product->name,
                    'code' => $currentSku->product->code,
                    'volume' => $currentSku->volume,
                    'rating' => $currentSku->rating,
                    'reviews_count' => $currentSku->reviews_count,
                    'question_count' => 0,
                    'images' => $currentSku->images ?? []
                ]
            ]);

        } else {
            $currentSku = Product::select(
                'products.id',
                'skus.id AS sku_id',
                'skus.volume',
                'skus.rating',
                'skus.images',
                'skus.reviews_count',
                'products.name',
                'products.code',
                'products.category_id',
                'categories.name AS category',
                'categories.code AS category_code',
                'brands.name AS brand',
                'brands.code AS brand_code',
                'brands.image AS brand_image',
                'countries.name AS country',
                'products.description',
                'products.application',
                'products.purpose',
                'products.effect',
                'products.age',
                'products.type_of_skin',

            )
                ->with([
                    'skus',
                    'ingredients' => function ($q) {
                        $q->orderBy('order', 'asc');
                    }
                ])
                ->join('skus', 'skus.product_id', '=', 'products.id')
                ->join('categories', 'categories.id', '=', 'products.category_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
                ->leftJoin('countries', 'countries.id', '=', 'brands.country_id')
                ->where('skus.id', $skuId)
                ->first();

            $skuPrices = DB::table('sku_store')
                ->select('sku_store.price', 'links.code AS link_code', 'stores.name', 'stores.image')
                ->join('links', 'links.id', '=', 'sku_store.link_id')
                ->join('stores', 'stores.id', '=', 'sku_store.store_id')
                ->where('sku_store.sku_id', $skuId)
                ->get();

        }

        if (!$currentSku) {
            return response()->json(['data' => ['message' => 'Not found']], 404);
        }
        return new ProductResource($currentSku, $skuPrices);
    }


    public function showComparedSkus(Request $request): AnonymousResourceCollection
    {
        $skus = Sku::with([
            'product' => function ($query) {
                $query->with([
                    'category',
                    'ingredients' => function ($q) {
                        $q->orderBy('order', 'asc');
                    },
                    'brand' => function ($q) {
                        $q->with('country');
                    }
                ]);
            },
            'prices'
        ])
            ->whereIn('id', $request->ids)
            ->get();

        return ComparedSkuResource::collection($skus);
    }
}
