<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin\Parser;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\DataProvider;
use App\Models\ParsingLink;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParsingLinkController extends Controller
{
    use DataProvider;

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) ($request->per_page ?? 30);
        $forPrice = (bool) ($request->forPrice ?? false);
        $storeId = (int) $request->store_id;

        $query = ParsingLink::select('id', 'link', DB::raw('DATE(created_at) as date'))
            ->where('store_id', $storeId)
            ->where('parsed', $forPrice ? 1 : 0);


        $result = $this->prepareModel($request, $query)->paginate($perPage);

        return response()->json(['data' => $result]);
    }



    public function storesWithUnparsedLinksCount(): JsonResponse
    {
        $result = Store::select(DB::raw("stores.id, stores.name, count(parsing_links.id) as countBeforeEnd"))
            ->join('parsing_links', 'stores.id', '=', 'parsing_links.store_id')
            ->where('parsing_links.parsed', 0)
            ->groupBy('stores.id', 'stores.name')
            ->get();

        return response()->json(['data' => $result]);
    }


    public function storesWithLinksCount(): JsonResponse
    {
        $result = Store::select(DB::raw("stores.id, stores.name, count(parsing_links.id) as count"))
            ->join('parsing_links', 'stores.id', '=', 'parsing_links.store_id')
            ->where('parsing_links.parsed', 1)
            ->groupBy('stores.id', 'stores.name')
            ->get();

        return response()->json(['data' => $result]);
    }
}
