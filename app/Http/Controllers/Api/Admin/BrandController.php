<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\DataProviderWithDTO;
use App\Http\Controllers\Traits\ParamsDTO;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use App\Services\ImageSavingService\ImageSavingService;
use App\Services\Parser\Text;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class BrandController extends Controller
{
    use DataProviderWithDTO;

    const IMAGES_FOLDER = 'public/image/brand/';


    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) ($request->per_page ?? 10);

        if ($perPage === -1) {
            $result = Brand::query()->select(['id', 'name'])->get();
            return response()->json(['data' => $result]);
        }

        $productsWithSkusQuery = DB::table('products')
            ->selectRaw('count(products.brand_id) AS sku_count, products.brand_id')
            ->join('skus', 'skus.product_id', '=', 'products.id')
            ->groupBy('products.brand_id');


        $query = DB::table('brands')
            ->select([
                'brands.id',
                'brands.name',
                'brands.image',
                'brands.description as description',
                'countries.name as country',
                'productsWithSkus.sku_count'
            ])
            ->joinSub($productsWithSkusQuery, 'productsWithSkus', function ($join) {
                $join->on( 'productsWithSkus.brand_id', '=', 'brands.id');
            })
            ->leftJoin('countries', 'brands.country_id', '=', 'countries.id');

        $paramsDto = new ParamsDTO(
            $request->input('filter', []),
            $request->input('sort', ''),
        );

        $result = $this->prepareModel($paramsDto, $query)->paginate($perPage);

        return response()->json(['data' => $result]);
    }


    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \App\Http\Resources\BrandResource
     */
    public function show(int $id): BrandResource
    {
        return new BrandResource(Brand::where('id', $id)->first());
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
        $params = $request->all();

        if ($request->image) {
            $params['image'] = $imageSavingService->saveOneImage($request->image, self::IMAGES_FOLDER, $request->name);
        }
        $params['code'] = Text::makeCode($params['name']);

        $newBrand = Brand::query()->create($params);

        return response()->json(['data' => $newBrand], Response::HTTP_CREATED);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Brand $brand
     * @param  \App\Services\ImageSavingService\ImageSavingService $imageSavingService
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Brand $brand, ImageSavingService $imageSavingService): JsonResponse
    {
        if ($brand) {
            $params = $request->all();

            if ($request->image) {
                $params['image'] = $imageSavingService->saveOneImage($request->image, self::IMAGES_FOLDER, $request->name);
            }
            $params['code'] = Text::makeCode($params['name']);

            $brand->update($params);

            return response()->json(['data' => [
                'status' => 'success',
                'message' => 'Бренд успешно обновлен'
            ]
            ]);
        }
        return response()->json([
            'message' =>'Not found'
        ], 404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy(int $id): void
    {
        Brand::destroy($id);
    }
}
