<?php

namespace App\Http\Controllers\Api\Admin\Parser;

use App\Http\Controllers\Controller;
use App\Models\PriceOption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PriceOptionController extends Controller
{
    public function index(Request $request): JsonResponse
{
    $storeId =  (int) $request->store_id;
    $result = PriceOption::where('store_id', $storeId)->first();

    return response()->json(['data' => isset($result->options) ? json_decode($result->options) : null]);
}


    public function updateOrCreate(Request $request): JsonResponse
    {
        $newOption = PriceOption::updateOrCreate(
            ['store_id' =>  $request->store_id],
            ['options' => json_encode($request->options)]
        );

        return response()->json(['data' => $newOption]);
    }
}
