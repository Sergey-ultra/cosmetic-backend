<?php


namespace App\Services\Parser\Contracts;


interface IPriceParser
{
    public function parsePricesByLink(string $link, string $priceTag): array;
}
