<?php

namespace App\Services\ImageLoadingService;

class ImageSavedPathDTO
{
    public function __construct(
        public readonly array|false $sizeOptions,
        public readonly string $imageSavePath
    ){}
}
