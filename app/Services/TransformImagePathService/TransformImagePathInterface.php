<?php

declare(strict_types=1);


namespace App\Services\TransformImagePathService;


interface TransformImagePathInterface
{
    public function getDestinationPath(string $filePath, string $folder): string;
}