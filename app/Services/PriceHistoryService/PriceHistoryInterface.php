<?php


namespace App\Services\PriceHistoryService;


interface PriceHistoryInterface
{
    public function makePriceDynamics(array $prices):array;
}