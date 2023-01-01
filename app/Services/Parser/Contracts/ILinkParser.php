<?php


namespace App\Services\Parser\Contracts;


interface ILinkParser
{
    public function parseProductLinks(
        string $categoryPageUrl,
        string $storeUrl,
        string $productLink,
        bool $relatedProductLink,
        string $nextPage,
        bool $isRelatedPageUrl
    ): array;
}