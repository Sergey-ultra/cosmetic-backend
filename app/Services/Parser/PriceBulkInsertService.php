<?php

declare(strict_types=1);

namespace App\Services\Parser;


use App\Models\PriceHistory;
use App\Models\SkuStore;
use Illuminate\Support\Facades\DB;


class PriceBulkInsertService
{
    public function priceBulkInsert(array $parsedPrices): bool
    {
        try {
            DB::beginTransaction();

            SkuStore::upsert(
                $parsedPrices,
                ['sku_id', 'store_id', 'link_id'],
                [
                    'price',
                    'fails_count'=> DB::raw('IF(price IS NULL, fails_count + 1, fails_count)')
                ]
            );

            DB::statement("SET foreign_key_checks=0");
            PriceHistory::insert($parsedPrices, []);

            DB::statement("SET foreign_key_checks=1");
            DB::commit();
            return true;
        } catch (\Throwable $e) {
            DB::rollback();
            throw $e;
        }
    }
}
