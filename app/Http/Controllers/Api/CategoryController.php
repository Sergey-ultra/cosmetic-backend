<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;


class CategoryController extends Controller
{
    public function all(): JsonResponse
    {
        $result = Category::query()
            ->select(['id', 'name', 'parent_id'])
            ->get();

        return response()->json(['data' =>  $result ]);
    }
    public function nested(): JsonResponse
    {
        $result = Category::query()
            ->select(['id', 'name', 'code', 'image'])
            ->whereNotNull('parent_id')
            ->get();

        return response()->json(['data' =>  $result ]);
    }


    public function byIds($ids): JsonResponse
    {
        $result = Category::query()
            ->select('id', 'name')
            ->whereIn('id', $ids)
            ->get();
        return response()->json(['data' => $result]);
    }
}
