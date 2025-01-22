<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin\Parser;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\DataProviderWithDTO;
use App\Http\Controllers\Traits\ParamsDTO;
use App\Http\Requests\Admin\StoresWithLinkCountRequest;
use App\Models\ParsingLink;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ParsingLinkController extends Controller
{
    use DataProviderWithDTO;

    public function index(Request $request): JsonResponse
    {
        $perPage = (int)($request->per_page ?? 30);
        $forPrice = (bool)($request->forPrice ?? false);
        $storeId = (int)$request->store_id;

        $columns = ['id', 'link', DB::raw('DATE(created_at) as date')];

        if (!$forPrice) {
            $columns[] = DB::raw("IF(body IS NULL, false, true) AS is_body_exist");
        }

        $query = ParsingLink::query()->select($columns)
            ->where('store_id', $storeId)
            ->where('parsed', $forPrice ? 1 : 0);

        $paramsDto = new ParamsDTO(
            $request->input('filter', []),
            $request->input('sort', ''),
        );

        $result = $this->prepareModel($paramsDto, $query)->paginate($perPage);

        return new JsonResponse(['data' => $result]);
    }


    public function storesWithLinksCount(StoresWithLinkCountRequest $request): JsonResponse
    {
        $result = Store::query()->select(DB::raw("stores.id, stores.name, count(parsing_links.id) as count"))
            ->join('parsing_links', 'stores.id', '=', 'parsing_links.store_id')
            ->where('parsing_links.parsed', $request->input('parsed'))
            ->groupBy('stores.id', 'stores.name')
            ->get();

        return response()->json(['data' => $result]);
    }

    /**
     * @param int $id
     * @return void
     */
    public function deleteBodyFromParsingLink(int $id): void
    {
        ParsingLink::query()->where('id', $id)->update(['body' => null]);
    }
}
