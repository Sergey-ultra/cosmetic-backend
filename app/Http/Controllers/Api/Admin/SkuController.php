<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\DataProvider;
use App\Http\Resources\Admin\ProductCollection;
use App\Http\Resources\Admin\ProductSingleResource;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Sku;
use App\Services\ImageSavingService\ImageSavingService;
use App\Services\Parser\Text;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class SkuController extends Controller
{
    use DataProvider;

    const IMAGES_FOLDER = 'public/image/sku/';
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \App\Http\Resources\Admin\ProductCollection
     */
    public function index(Request $request): ProductCollection
    {
        $perPage =  $request->per_page ?? 10;

        $query = DB::table('products')
            ->select([
                'skus.id as id',
                'categories.name as category',
                'brands.name as brand',
                'products.name as name',
                'products.code as code',
                'skus.volume as volume',
                'skus.images as images',
                'skus.created_at as created_at'
            ])
            ->join('skus', 'skus.product_id', '=', 'products.id')
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
        ;


        $result = $this->prepareModel($request, $query, true)->paginate($perPage);

        return new ProductCollection($result);
    }




    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \App\Http\Resources\Admin\ProductSingleResource
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Services\ImageSavingService\ImageSavingService $imageSavingService
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, ImageSavingService $imageSavingService): JsonResponse
    {
        $brand = Brand::find($request->brand_id);
        $code = Text::makeProductCode($request->name, $brand->name);


        $imageUrls = [];
        if ($request->has('images')) {
            $fileName = $code. '-' . preg_replace('#\s+#', '', $request->volume);
            $imageUrls = $imageSavingService->imageSave(
                $request->images,
                self::IMAGES_FOLDER,
                $fileName
            );
        }

        $imageUrls = json_encode($imageUrls);

        $createdProduct = Product::create([
            'brand_id' => $request->brand_id,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'code' => $code
        ]);


        //Sku::create([
        //'volume' => $request->volume,
        // 'product_id => $createdProduct->id
         //]);
        return response()->json([
            'data' => [
                'status' => 'success',
                'data' => $createdProduct
            ]
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Sku $sku
     * @param  \App\Services\ImageSavingService\ImageSavingService $imageSavingService
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Sku $sku, ImageSavingService $imageSavingService): JsonResponse
    {
        if ($sku) {
            $brand = Brand::find($request->brand_id);
            $code = Text::makeProductCode($request->name, $brand->name);


            $imageUrls = [];
            if ($request->has('images') && count($request->images)) {
                $fileName = $code . '-' . preg_replace('#\s+#', '', $request->volume);
                $imageUrls = $imageSavingService->imageSave($request->images, self::IMAGES_FOLDER, $fileName);
            }


            $sku->update([
                'volume' => $request->volume,
                'images' => json_encode($imageUrls)
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

        return response()->json([
            'status'=> true,
            'message' =>'Not found'
        ], 404);
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
