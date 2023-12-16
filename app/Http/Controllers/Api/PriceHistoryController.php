<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PriceHistory;
use App\Services\PriceHistoryService\PriceHistoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class PriceHistoryController extends Controller
{

    public function index(Request $request, PriceHistoryService $priceService): JsonResponse
    {
        $skuId = $request->integer('sku_id');
        $sqlMode = $request->boolean('sql_mode', true);

        $result = $priceService->makePriceDynamicsBySkuId($skuId, $sqlMode);

        return response()->json(['data' =>  $result]);
    }
}
