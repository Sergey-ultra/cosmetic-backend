<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sku;
use App\Services\TreeService\TreeInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function all(TreeInterface $treeService): JsonResponse
    {
        $categories = Category::query()
            ->select(['id', 'name', 'code', 'parent_id'])
            ->get()
            ->toArray();

        $result = $treeService->buildTree($categories, 'parent_id');

        return response()->json(['data' =>  $result ]);
    }

    public function popularCategories(): JsonResponse
    {
        $result = Category::query()
            ->select([
                sprintf('%s.id', Category::TABLE),
                sprintf('%s.name', Category::TABLE),
                sprintf('%s.code', Category::TABLE),
                sprintf('%s.image', Category::TABLE),
                DB::raw(sprintf('count(%s.id) AS sku_count', Sku::TABLE)),
            ])
            ->join(
                Product::TABLE,
                sprintf('%s.category_id', Product::TABLE),
                '=',
                sprintf('%s.id', Category::TABLE)
            )
            ->join(
                Sku::TABLE,
                sprintf('%s.product_id', Sku::TABLE),
                '=',
                sprintf('%s.id', Product::TABLE)
            )
            ->whereNotNull(sprintf('%s.parent_id', Category::TABLE))
            ->groupBy(
                sprintf('%s.id', Category::TABLE),
                sprintf('%s.name', Category::TABLE),
                sprintf('%s.code', Category::TABLE),
                sprintf('%s.image', Category::TABLE),
            )
            ->orderByDesc('sku_count')
            ->limit(8)
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
