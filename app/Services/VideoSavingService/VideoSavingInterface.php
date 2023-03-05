<?php


namespace App\Services\VideoSavingService;


interface VideoSavingInterface
{
    public function saveOneAsFile(string $stringData, string $folder, string $fileName): array;
    public function saveOneAsBase64(string $stringData, string $folder, string $fileName): array;
}
