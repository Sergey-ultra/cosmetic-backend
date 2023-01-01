<?php


namespace App\Services\Parser\Contracts;


interface IBrandParser
{
    public function parseBrandNames(string $brandPage, string $brandTag): array;
}