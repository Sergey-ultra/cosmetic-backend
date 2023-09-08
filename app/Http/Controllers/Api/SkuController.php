<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SkuListRequest;
use App\Http\Requests\SkuRequest;
use App\Http\Resources\ComparedSkuResource;
use App\Http\Resources\ProductResource;
use App\Jobs\AdminNotificationJob;
use App\Jobs\SearchLogJob;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sku;
use App\Models\UserMessage;
use App\Repositories\SkuRepository\DTO\SkuDTO;
use App\Repositories\SkuRepository\SkuRepository;
use App\Services\EntityStatus;
use App\Services\MessageService\MessageServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;


class SkuController extends Controller
{
    const SMALL_IMAGES_FOLDER = 'small';


    /**
     * @param SkuListRequest $request
     * @param SkuRepository $skuRepository
     * @return JsonResponse
     */
    public function mainIndex(SkuListRequest $request, SkuRepository $skuRepository): JsonResponse
    {
        $categoryCode = $request->input('category_code');
        $brandCode = $request->input('brand_code');
        $search = $request->input('search');


        $result = [];
        $entityId = null;
        if ($categoryCode) {

            $category = Category::query()->select(['id', 'name'])->where('code', $categoryCode)->first();
            if (!$category) {
                return response()->json(['error' => 'Not Found'], Response::HTTP_NOT_FOUND);
            }

            $result["category"] = $category;
            $mode = 'category';
            $entityId = $category->id;

        } else if ($brandCode) {

            $brand = Brand::query()->select(['id', 'name'])->where('code', $brandCode)->first();
            if (!$brand) {
                return response()->json(['error' => 'Not Found'], Response::HTTP_NOT_FOUND);
            }
            $result["brand"] = $brand;
            $mode = 'brand';
            $entityId = $brand->id;
        } else if ($search) {
            SearchLogJob::dispatch($search);
            $mode ='search';
        }


        $skuRepository->setSmallImagesFolder(self::SMALL_IMAGES_FOLDER);
        $result['products'] = $skuRepository
            ->setMode($mode, $entityId)
            ->getList($request->getDto());

        return response()->json($result);
    }

    public function popularTenSkus(SkuRepository $skuRepository): JsonResponse
    {
        $result = $skuRepository->popularTenSkus();
        return response()->json(['data' => $result]);
    }

    public function mySkus(): JsonResponse
    {
        $userId = Auth::guard('api')->id();
        $result = Sku::query()
            ->select(
                sprintf('%s.id', Sku::TABLE),
                sprintf('%s.volume', Sku::TABLE),
                sprintf('%s.name', Product::TABLE),
                sprintf('%s.code AS product_code', Product::TABLE),
            )
            ->join(
                Product::TABLE,
                sprintf('%s.product_id', Sku::TABLE),
                '=',
                sprintf('%s.id', Product::TABLE)
            )
            ->where(sprintf('%s.user_id', Sku::TABLE), $userId)
            ->whereNot(sprintf('%s.status', Sku::TABLE), EntityStatus::PUBLISHED)
            ->get();

        return response()->json(['data' => $result]);
    }


    /**
     * @params Request $request
     * @return JsonResponse
     */
    public function viewed(Request $request): JsonResponse
    {
        $ids = $request->ids;

        $result = Product::query()
            ->select(
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
     * @params int $skuId
     * @params Request $request
     * @return ProductResource | JsonResponse
     */

    public function bySkuId(int $skuId, Request $request): ProductResource|JsonResponse
    {
        if ($request->view === 'compact') {
            $currentSku = Sku::with('product')->find($skuId);
            return response()->json([
                'data' => [
                    'id' => $currentSku->id,
                    'name' => $currentSku->product->name,
                    'code' => $currentSku->product->code,
                    'volume' => $currentSku->volume,
                    'rating' => round($currentSku->rating, 1),
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
                    'ingredients' => function ($query) {
                        $query
                            ->with('activeIds')
                            ->orderBy('order', 'asc')
                        ;
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

    public function store(
        SkuRequest $request,
        SkuRepository $skuService,
        MessageServiceInterface $messageService
    ): JsonResponse
    {
        $brand = Brand::query()->find($request->input('brand_id'));

        $skuDto = new SkuDTO(
            $request->input('category_id'),
            $request->input('brand_id'),
            $request->input('name'),
            $brand->name,
            $request->input('description'),
            $request->input('volume'),
            $request->input('images'),
            'moderated'
        );

        try {
            $newSku = $skuService->createNewSku($skuDto);

            $messageService->addSku($newSku['sku_code'], $newSku['name'], Auth::guard('api')->user()->id);

            $message = sprintf('Добавлен новый sku c именем %s', $newSku['name']);
            AdminNotificationJob::dispatch($message);

            return response()->json(['data' => $newSku], Response::HTTP_CREATED);
        } catch(Throwable $e) {
            $message = $e->getMessage();
            if (23000 === (int)$e->getCode()) {
                $message = sprintf(
                    'Товарное предложение с именем %s и брендом %s уже существует',
                    $request->input('name'),
                    $brand->name,
                );
            }
            $response['error'] = ['message' => $message];
            return response()->json($response,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
