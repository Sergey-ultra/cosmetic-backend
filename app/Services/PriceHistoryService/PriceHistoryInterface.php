<?php


namespace App\Services\PriceHistoryService;


interface PriceHistoryInterface
{
    public function makePriceDynamicsBySkuId(int $skuId, bool $sqlMode = true): array;
}
