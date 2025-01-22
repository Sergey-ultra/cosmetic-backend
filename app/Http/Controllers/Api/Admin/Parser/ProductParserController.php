<?php

namespace App\Http\Controllers\Api\Admin\Parser;

use App\Helpers\MemoryUsage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductParsingRequest;
use App\Jobs\CompressImageJob;
use App\Services\Parser\ProductCardCrawlerParser;
use App\Services\Parser\ProductParserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductParserController extends Controller
{
    public function parseProductByLinkIds(ProductParsingRequest $request, ProductParserService $productParserService): JsonResponse
    {
        set_time_limit(7200);

        $storeId = (int)$request->storeId;
        $linkIds = (array)$request->input('linkIds', []);


        $isLoadToDb = (bool)($request->isLoadToDb ?? false);
        $isInsertIngredients = (bool)($request->isInsertIngredients ?? false);
        //зачем мы передаем эти значения с фронта?
        $brandId = $request->brandId ? (int)$request->brandId : null;

        $result = $productParserService->parseProducts($isLoadToDb, $linkIds, $storeId, $isInsertIngredients, $brandId);

        return response()->json(['data' => $result, 'size' => MemoryUsage::getMemoryUsage()]);
    }

    public function compressAllUncompressedImages(): JsonResponse
    {
        set_time_limit(7200);

        $fileList = scandir(Storage::path(ProductCardCrawlerParser::DESTINATION_FOLDER));
        $smallFileList = scandir(Storage::path(ProductCardCrawlerParser::DESTINATION_FOLDER . '/small'));

        $diff = array_values(array_diff($fileList, $smallFileList));

        foreach ($diff as $key => $filename) {
            if ($key > 20) {
                break;
            }
            $destinationPath = ProductCardCrawlerParser::DESTINATION_FOLDER . $filename;
            $filePath = Storage::path($destinationPath);

            CompressImageJob::dispatch($filePath);

        }

        return response()->json(['data' => [count($fileList), count($smallFileList), $diff]]);
    }
}
