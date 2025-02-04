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
        $result = LinkOption::query()->where(['store_id' => $storeId, 'category_id' => $categoryId])->first();

        return response()->json(['data' => $result?->options]);
    }


    public function updateOrCreate(Request $request): JsonResponse
    {
        $newOption = LinkOption::query()->updateOrCreate(
            ['store_id' =>  $request->get('store_id'), 'category_id' => $request->get('category_id')],
            ['options' => $request->get('options')]
        );

        return response()->json(['data' => $newOption]);
    }
}
