<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\DataProvider;
use App\Models\Category;
use App\Services\ImageSavingService\ImageSavingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use DataProvider;

    const IMAGES_FOLDER = 'public/image/store/';

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int)  ($request->per_page ?? 10);

        if ($perPage === -1) {
            $result = Category::select(['id', 'name'])->whereNotNull('parent_id')->get();
            return response()->json(['data' => $result]);
        }

        $query = Category::select(['id', 'image', 'name', 'code', 'description', 'image',  'parent_id']);
        $result = $this->prepareModel($request, $query)->paginate($perPage);

        return response()->json(['data' => $result]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Services\ImageSavingService\ImageSavingService $imageSavingService
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, ImageSavingService $imageSavingService): JsonResponse
    {
        $params = $request->all();

        if ($request->image){
            $params['image'] = $imageSavingService->saveOneImage($request->image, self::IMAGES_FOLDER, $request->name, false);
        }

        Category::create($params);

        return response()->json([
            'status'=> true,
            'message' =>'Категория успешно создана'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $category = Category::find($id);

        $category['skus'] = $category->products->map->skus->flatten();
        return response()->json(['data' => $category]);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category $category
     * @param  \App\Services\ImageSavingService\ImageSavingService $imageSavingService
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Category $category, ImageSavingService $imageSavingService): JsonResponse
    {
        if ($category) {
            $params = $request->all();

            if ($request->image){
                $params['image'] = $imageSavingService->saveOneImage($request->image, self::IMAGES_FOLDER, $request->name, false);
            }

            $category->update($params);
            return response()->json([
                'status'=> true,
                'message' =>'Категория успешно обновлена'
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
    public function destroy(int $id): void
    {
        Category::destroy($id);
    }

}
