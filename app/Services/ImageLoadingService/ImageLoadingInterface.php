<?php


namespace App\Services\ImageLoadingService;


interface ImageLoadingInterface
{
    public function loadingImage(string $destinationFolder, string $sourceUrl, string $fileName): ImageSavedPathDTO;
}
