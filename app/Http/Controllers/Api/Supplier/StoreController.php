<?php

declare(strict_types=1);


namespace App\Http\Controllers\Api\Supplier;


use App\Http\Controllers\Controller;
use App\Http\Requests\Supplier\StoreCreateRequest;
use App\Http\Requests\Supplier\StorePriceFileCreateRequest;
use App\Http\Requests\Supplier\StoreUpdateRequest;
use App\Jobs\AdminNotificationJob;
use App\Models\Store;
use App\Models\StorePriceFile;
use App\Services\ImageSavingService\ImageSavingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class StoreController extends Controller
{
    const IMAGES_FOLDER = 'public/image/store/';

    public function index()
    {
        $stores = Store::select('id', 'name', 'image')->get();
        return response()->json(['data' => $stores]);
    }

    public function my()
    {
        $my = DB::table('store_price_files')->select(
            'store_price_files.id',
            DB::raw('IF(store_price_files.store_id, stores.name, store_price_files.name) AS name'),
            DB::raw('IF(store_price_files.store_id, stores.link, store_price_files.link) AS link'),
            DB::raw('IF(store_price_files.store_id, stores.image, store_price_files.image) AS image'),
            'store_price_files.file_url',
            'store_price_files.store_id'
        )
            ->leftJoin('stores', 'store_price_files.store_id', '=', 'stores.id')
            ->where('store_price_files.user_id', Auth::user()->id)
            ->first();

        return response()->json(['data' => $my]);
    }

    public function store(StoreCreateRequest $request): JsonResponse
    {

        StorePriceFile::create([
            'name' => $request->name,
            'link' => $request->store_url,
            'user_id' => Auth::user()->id,
            'file_url' => $request->price_url,
            'status' => 'moderated'
        ]);

        if (request()->ip() !== config('telegrambot.admin_ip')) {
            $message = "Зарегистированновый поставщик";
            AdminNotificationJob::dispatch($message);
        }

        $my = DB::table('store_price_files')->select(
            'store_price_files.id',
            DB::raw('IF(store_price_files.store_id, stores.name, store_price_files.name) AS name'),
            DB::raw('IF(store_price_files.store_id, stores.link, store_price_files.link) AS link'),
            DB::raw('IF(store_price_files.store_id, stores.image, store_price_files.image) AS image'),
            'store_price_files.file_url',
            'store_price_files.store_id'
        )
            ->leftJoin('stores', 'store_price_files.store_id', '=', 'stores.id')
            ->where('store_price_files.user_id', Auth::user()->id)
            ->first();
        return response()->json([
            'data' => [
                'status' => 'success',
                'data' => $my
            ]
        ]);
    }

    public function update(int $id, StoreUpdateRequest $request, ImageSavingService $imageSavingService): JsonResponse
    {
        $existing = StorePriceFile::where([
            'id' => $id,
            'user_id'=> Auth::user()->id
        ])->first();

        if (!$existing) {
            return response()->json(['data' => ['status' => 'error']], 404);
        }


        $params = $request->all();
        if ($request->image) {
            $params['image'] = $imageSavingService->saveOneImage($request->image, self::IMAGES_FOLDER, $request->name);
        }
        $params['status'] = 'moderated';

        $existing->update($params);

        if (request()->ip() !== config('telegrambot.admin_ip')) {
            $message = "Поставщик с id $existing->id обновил данные";
            AdminNotificationJob::dispatch($message);
        }

        $my = DB::table('store_price_files')->select(
            'store_price_files.id',
            DB::raw('IF(store_price_files.store_id, stores.name, store_price_files.name) AS name'),
            DB::raw('IF(store_price_files.store_id, stores.link, store_price_files.link) AS link'),
            DB::raw('IF(store_price_files.store_id, stores.image, store_price_files.image) AS image'),
            'store_price_files.file_url',
            'store_price_files.store_id'
        )
            ->leftJoin('stores', 'store_price_files.store_id', '=', 'stores.id')
            ->where('store_price_files.user_id', Auth::user()->id)
            ->first();

        return response()->json([
            'data' => [
                'status' => 'success',
                'data' => $my
            ]
        ]);
    }

    public function addPriceFile(int $id, StorePriceFileCreateRequest $request): JsonResponse
    {
        StorePriceFile::create([
            'store_id' => $id,
            'user_id' => Auth::user()->id,
            'file_url' => $request->price_url
        ]);

        $my = DB::table('store_price_files')->select(
            'store_price_files.id',
            DB::raw('IF(store_price_files.store_id, stores.name, store_price_files.name) AS name'),
            DB::raw('IF(store_price_files.store_id, stores.link, store_price_files.link) AS link'),
            DB::raw('IF(store_price_files.store_id, stores.image, store_price_files.image) AS image'),
            'store_price_files.file_url',
            'store_price_files.store_id'
        )
            ->leftJoin('stores', 'store_price_files.store_id', '=', 'stores.id')
            ->where('store_price_files.user_id', Auth::user()->id)
            ->first();

        return response()->json([
            'data' => [
                'status' => 'success',
                'data' => $my
            ]
        ]);
    }
}