<?php


namespace App\Services\Parser\Contracts;


interface ILinkParser
{
    public function setParsingOptions(
        int     $linkOptionId,
        array   $body,
        ?string $nextPage,
        string  $link,
        string  $targetUrl
    ): void;
    public function parseLinksFromPage(string $categoryPageUrl): array;
    public function getParsedBodies(): array;
}
