<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\DataProviderWithDTO;
use App\Http\Controllers\Traits\ParamsDTO;
use App\Http\Resources\Admin\ProductCollection;
use App\Http\Resources\Admin\ProductSingleResource;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Sku;
use App\Models\SkuStore;
use App\Models\User;
use App\Repositories\SkuRepository\DTO\SkuDTO;
use App\Repositories\SkuRepository\SkuRepository;
use App\Services\ImageSavingService\ImageSavingService;
use App\Services\Parser\Text;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class SkuController extends Controller
{
    use DataProviderWithDTO;

    const IMAGES_FOLDER = 'public/image/sku/';

    /**
     * @param Request $request
     * @return ProductCollection
     */
    public function index(Request $request): ProductCollection
    {
        $perPage = (int)($request->per_page ?? 10);

        $ingredientProductSubQuery = DB::table('ingredient_product')
            ->select('product_id')
            ->groupBy('product_id');

        $currentPricesCountSubQuery = DB::table(SkuStore::TABLE)
            ->select('sku_id', DB::raw('count(sku_id) AS link_count'))
            ->groupBy('sku_id');

        $query = Sku::query()
            ->select([
                'skus.id',
                'categories.name as category',
                'brands.name as brand',
                'products.name',
                'products.code',
                'skus.volume',
                'skus.images',
                'skus.created_at',
                'skus.status',
                'users.name as user_name',
                'currentPrices.link_count',
                DB::raw("IF(ip.product_id IS NULL, false, true) AS is_ingredients_exist"),

            ])
            ->leftJoinSub($currentPricesCountSubQuery, 'currentPrices', function($join) {
                $join->on('currentPrices.sku_id', '=', 'skus.id');
            })
            ->join('products', 'skus.product_id', '=', 'products.id')
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->leftJoin(User::TABLE, sprintf('%s.id', User::TABLE), '=', sprintf('%s.user_id', Sku::TABLE))
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoinSub($ingredientProductSubQuery, 'ip', function ($join) {
                $join->on('ip.product_id', '=', 'products.id');
            });

        $paramsDto = new ParamsDTO(
            $request->input('filter', []),
            $request->input('sort', ''),
        );

        $result = $this->prepareModel($paramsDto, $query)->paginate($perPage);

        return new ProductCollection($result);
    }




    /**
     * @param  int  $id
     * @return ProductSingleResource
     */
    public function show(int $id): ProductSingleResource
    {
        $result = Product::select([
            'products.name',
            'products.brand_id',
            'products.category_id',
            'products.description',
            'skus.volume',
            'skus.images',
            'skus.id',
        ])
            ->leftJoin('skus', 'skus.product_id', '=', 'products.id')
            ->where('skus.id', $id)
            ->first();

        return new ProductSingleResource($result);
    }

    /**
     * @param Request $request
     * @param ImageSavingService $imageSavingService
     * @return JsonResponse
     */
    public function store(Request $request, SkuRepository $skuService, ImageSavingService $imageSavingService): JsonResponse
    {
        $brand = Brand::query()->find($request->input('brand_id'));

        $imageUrls = [];

        if ($request->has('images')) {
            $code = Text::makeProductCode($request->input('name'), $brand->name);

            $fileName = sprintf('%s-%s', $code, preg_replace('#\s+#', '',  $request->input('volume')));

            $imageUrls = $imageSavingService->saveImages(
                $request->input('images'),
                self::IMAGES_FOLDER,
                $fileName
            );
        }

        $skuDto = new SkuDTO(
            $request->input('category_id'),
            $request->input('brand_id'),
            $request->input('name'),
            $brand->name,
            $request->input('description'),
            $request->input('volume'),
            $imageUrls,
            'published'
        );


        try {
            $newSku = $skuService->createNewSku($skuDto);

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

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Sku $sku
     * @param ImageSavingService $imageSavingService
     * @return JsonResponse
     */
    public function update(Request $request, Sku $sku, ImageSavingService $imageSavingService): JsonResponse
    {
        if (!$sku) {
            return response()->json([
                'status'=> true,
                'message' =>'Sku not found'
            ], Response::HTTP_NOT_FOUND);
        }
        $brand = Brand::query()->find($request->input('brand_id'));

        if (!$brand) {
            return response()->json([
                'status'=> true,
                'message' =>'Brand not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $code = Text::makeProductCode($request->input('name'), $brand->name);


        $imageUrls = [];
        if ($request->has('images') && count($request->images)) {
            $fileName = $code . '-' . preg_replace('#\s+#', '', $request->volume);
            $imageUrls = $imageSavingService->saveImages($request->images, self::IMAGES_FOLDER, $fileName);
        }


        $sku->update([
            'volume' => $request->volume,
            'images' => $imageUrls,
        ]);


        $sku->product->update([
            'brand_id' => $request->brand_id,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'code' => $code
        ]);

        return response()->json([
            'data' => [
                'status' => 'success',
                'message' => 'Товарное предложение успешно обновлено'
            ]
        ]);
    }




    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy(int $id)
    {
        Sku::destroy($id);
    }
}
