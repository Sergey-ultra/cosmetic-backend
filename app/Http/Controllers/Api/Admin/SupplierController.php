<?php

declare(strict_types=1);


namespace App\Http\Controllers\Api\Admin;


use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\DataProvider;
use App\Models\StorePriceFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    use DataProvider;

    const IMAGES_FOLDER = 'public/image/supplier/';

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) ($request->per_page ?? 10);

//        if ($perPage === -1) {
//            $stores = Store::select('id', 'name')->get();
//            return response()->json(['data' => $stores]);
//        }



        $query =  DB::table('store_price_files')->select(
            'store_price_files.id',
            DB::raw('IF(store_price_files.store_id, stores.name, store_price_files.name) AS name'),
            DB::raw('IF(store_price_files.store_id, stores.link, store_price_files.link) AS link'),
            DB::raw('IF(store_price_files.store_id, stores.image, store_price_files.image) AS image'),
            'store_price_files.file_url',
            'store_price_files.store_id',
            'store_price_files.status'
        )
            ->leftJoin('stores', 'store_price_files.store_id', '=', 'stores.id')
           ;



        $result = $this->prepareModel($request, $query, true)->paginate($perPage);

        return response()->json(['data' => $result]);
    }

    public function setStatus(int $id, Request $request)
    {
        $supplier = StorePriceFile::find($id);
        if (!$supplier) {
            return response()->json(['data' => ['status' => 'error']], 404);
        }
        $supplier->update(['status' => $request->status]);

        return response()->json(['data' => ['status' => 'success']]);
    }
}
