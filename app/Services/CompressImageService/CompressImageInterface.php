<?php

declare(strict_types=1);


namespace App\Services\CompressImageService;


interface CompressImageInterface
{
    public function compress(string $filePath);
}