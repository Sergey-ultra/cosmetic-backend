<?php

namespace App\Services\UrlService;

interface IUrlService
{
    public function relativeUrlToAbsolute(string $relativeUrl, string $base): string;
}
