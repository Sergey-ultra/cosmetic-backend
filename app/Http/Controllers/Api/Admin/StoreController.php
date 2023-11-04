<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\DataProviderWithDTO;
use App\Http\Controllers\Traits\ParamsDTO;
use App\Models\Store;
use App\Services\ImageSavingService\ImageSavingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    use DataProviderWithDTO;

    const IMAGES_FOLDER = 'public/image/store/';

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
            $stores = Store::select('id', 'name')->get();
            return response()->json(['data' => $stores]);
        }

        $linksQuery = DB::table('links')
            ->selectRaw('count(store_id) AS links_count, store_id')
            ->groupBy('store_id')
        ;

        $query = DB::table('stores', 's')
            ->select(
                's.id',
                's.name',
                's.link',
                's.image',
                's.price_parsing_status',
                's.check_images_count',
                'l.links_count'
            )
            ->leftJoinSub($linksQuery, 'l', function ($join) {
                $join->on('l.store_id', '=', 's.id');
            });

        $paramsDto = new ParamsDTO(
            $request->input('filter', []),
            $request->input('sort', ''),
        );

        $result = $this->prepareModel($paramsDto, $query)->paginate($perPage);

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
            $params['image'] = $imageSavingService->saveOneImage($request->image, self::IMAGES_FOLDER, $request->name);
        }
        $params['status'] = 'published';

        Store::create($params);

        return response()->json([
            'data' => [
                'status' => true,
                'message' => 'Магазин успешно создана'
            ]
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $store = Store::find($id);
        return response()->json($store);
    }

    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePriceParsingStatus(Request $request): JsonResponse
    {
        $store = Store::find($request->id);
        if (!$store) {
            return response()->json(['data' => ['status' => false]]);
        }

        $store->update(['price_parsing_status' => $request->status]);
        return response()->json(['data' => ['status' => true]]);
    }


    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeCheckingImageCountStatus(Request $request): JsonResponse
    {
        $store = Store::find($request->id);
        if (!$store) {
            return response()->json(['data' => ['status' => false]]);
        }

        $store->update(['check_images_count' => $request->status]);
        return response()->json(['data' => ['status' => true]]);
    }




    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Store $store
     * @param  \App\Services\ImageSavingService\ImageSavingService $imageSavingService
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Store $store, ImageSavingService $imageSavingService): JsonResponse
    {
        if ($store) {
            $params = $request->all();

            if ($request->image){
                $params['image'] = $imageSavingService->saveOneImage(
                    $request->image,
                    self::IMAGES_FOLDER,
                    $request->name
                );
            }

            $store->update($params);

            return response()->json([
                'data' => [
                    'status' => true,
                    'message' => 'Магазин успешно обновлен'
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
    public function destroy(int $id): void
    {
        Store::destroy($id);
    }
}
