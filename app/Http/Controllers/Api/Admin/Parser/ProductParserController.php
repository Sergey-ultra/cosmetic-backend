<?php

namespace App\Http\Controllers\Api\Admin\Parser;

use App\Http\Controllers\Controller;
use App\Services\Parser\ProductParserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductParserController extends Controller
{
    public function parseProductByLinkIds(Request $request, ProductParserService $productParserService): JsonResponse
    {
        set_time_limit(7200);

        $isLoadToDb = (bool) ($request->isLoadToDb ?? false);
        $linkIds = (array) $request->linkIds;

        //зачем мы передаем эти значения с фронта?
        $brandId = (int) $request->brandId;
        $storeId = (int) $request->storeId;

        $result = $productParserService->parseProducts($isLoadToDb, $linkIds, $storeId, $brandId);

        return response()->json(['data' => $result]);
    }
}
