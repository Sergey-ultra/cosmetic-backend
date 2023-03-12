<?php


namespace App\Services\Parser\Contracts;


interface ILinkParser
{
    public function setParsingOptions(
        int     $linkOptionId,
        array   $body,
        ?string $nextPage,
        string  $productLink,
        string  $storeUrl
    ): void;
    public function parseProductLinks(string $categoryPageUrl): array;
    public function getParsedBodies(): array;
}
