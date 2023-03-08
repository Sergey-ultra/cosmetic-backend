<?php

namespace App\Services\ImageSavingService;

interface ImageSavingInterface
{
    public function imageSave(array $images, string $folder, string $fileName, bool $isCompress, array $options): array;

    public function saveOneImage(string $stringData, string $folder, string $fileName, bool $isCompress, array $options): string;
}
