<?php


namespace App\Services\VideoSavingService;


interface VideoSavingInterface
{
    public function saveOneFile(string $stringData, string $folder, string $fileName): array;
}
