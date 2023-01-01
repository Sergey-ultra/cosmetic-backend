<?php

namespace App\Http\Controllers\Api\Admin\Parser;

use App\Http\Controllers\Controller;
use App\Models\LinkOption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LinkOptionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $storeId =  (int) $request->store_id;
        $categoryId =  (int) $request->category_id;
        $result = LinkOption::where(['store_id' => $storeId, 'category_id' => $categoryId])->first();

        return response()->json(['data' => isset($result->options) ? json_decode($result->options) : null]);
    }


    public function updateOrCreate(Request $request): JsonResponse
    {
        $newPriceOption = LinkOption::updateOrCreate(
            ['store_id' =>  $request->store_id, 'category_id' => $request->category_id],
            ['options' => $request->options]
        );

        return response()->json(['data' => $newPriceOption]);
    }
}
