<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\LinkOption;

final class LinkOptionRepository
{
    public function getByStoreIdAndCategoryId(int $storeId, int $categoryId): ?LinkOption
    {
        $linkOption = LinkOption::query()
            ->select(
                'link_options.id',
                'link_options.options',
                'stores.link'
            )
            ->with('pages')
            ->join('stores', 'stores.id', '=', 'link_options.store_id')
            ->where(['link_options.store_id' => $storeId, 'link_options.category_id' => $categoryId])
            ->first();
        if (!$linkOption) {
            return response()->json(['data' => ['message' => 'нет настроек']]);
        }


        $bodyArray = $linkOption->pages
            ? $linkOption->pages
                ->map(fn(array $item) => json_decode($item['body'] ,true))
                ->all()
            : [];

    }
}
