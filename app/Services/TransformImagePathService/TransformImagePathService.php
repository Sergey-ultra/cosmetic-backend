<?php
declare(strict_types=1);


namespace App\Services\TransformImagePathService;


class TransformImagePathService implements TransformImagePathInterface
{
    public function getDestinationPath(string $filePath, string $folder): string
    {
        $imagePathParts = explode('/', $filePath);
        $imageName = $imagePathParts[count($imagePathParts) - 1];
        $imagePathParts[count($imagePathParts) - 1] = $folder;
        $imagePathParts[] = $imageName;
        return implode('/',$imagePathParts);
    }
}