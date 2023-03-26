<?php

namespace App\Http\Controllers\Api\Admin\Parser;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductParsingRequest;
use App\Services\Parser\ProductParserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductParserController extends Controller
{
    public function parseProductByLinkIds(ProductParsingRequest $request, ProductParserService $productParserService): JsonResponse
    {
        set_time_limit(7200);

        $storeId = (int)$request->storeId;
        $linkIds = (array)$request->linkIds;


        $isLoadToDb = (bool)($request->isLoadToDb ?? false);
        $isInsertIngredients = (bool)($request->isInsertIngredients ?? false);
        //зачем мы передаем эти значения с фронта?
        $brandId = $request->brandId ? (int)$request->brandId : null;

        $result = $productParserService->parseProducts($isLoadToDb, $linkIds, $storeId, $isInsertIngredients, $brandId);

        return response()->json(['data' => $result]);
    }
}
