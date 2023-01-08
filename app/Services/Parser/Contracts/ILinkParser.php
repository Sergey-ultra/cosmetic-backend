<?php


namespace App\Services\Parser\Contracts;


interface ILinkParser
{
    public function parseProductLinks(string $categoryPageUrl): array;
}
