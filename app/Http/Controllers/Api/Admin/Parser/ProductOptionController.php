<?php

namespace App\Http\Controllers\Api\Admin\Parser;

use App\Http\Controllers\Controller;
use App\Models\ProductOption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductOptionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $storeId =  (int) $request->store_id;
        $result = ProductOption::where('store_id', $storeId)->first();

        return response()->json(['data' => isset($result->options) ? json_decode($result->options) : null]);
    }


    public function updateOrCreate(Request $request): JsonResponse
    {
        $newOption = ProductOption::updateOrCreate(
            ['store_id' =>  $request->store_id],
            ['options' => $request->options]
        );

        return response()->json(['data' => $newOption]);
    }
}
