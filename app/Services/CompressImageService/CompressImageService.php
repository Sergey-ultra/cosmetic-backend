<?php

declare(strict_types=1);

namespace App\Services\CompressImageService;


use Illuminate\Support\Facades\Log;
use Imagick;
use ImagickException;

class CompressImageService implements CompressImageInterface
{
    public function compress(string $filePath, array $options = [['folder' => 'small', 'width' => 200, 'height' => 200]]): array
    {

        try {
            $im = new Imagick();
            $im->pingImage($filePath);
            $im->readImage($filePath);

            foreach ($options as $option) {
                $im->resizeImage($option['width'], $option['height'], Imagick::FILTER_CATROM, 1, TRUE);
                $im->setImageFormat("webp");
                $im->setOption('webp:method', '6');
                $im->writeImage($this->getDestinationPath($filePath, $option['folder']));
            }

            return ['status' => true];
        } catch (ImagickException $e) {
            Log::error((string)$e);
            return ['status' => false, 'message' => $e->getMessage()];
        }

//
//        $imagickSrc = new Imagick($filePath);
//        //$compressionList = [Imagick::COMPRESSION_JPEG2000];
//        $imagickDest = new Imagick;
//        $imagickDest->setCompression(80);
//        $imagickDest->setCompressionQuality(80);
//        $imagickDest->newPseudoImage(200,200,'canvas:white');
//        $imagickDest->compositeImage($imagickSrc, Imagick::COMPOSITE_ATOP,0,0);
//        $imagickDest->setImageFormat("jpeg");
//        $imagickDest->writeImage($this->getDestinationPath($filePath));
    }

    protected function getDestinationPath(string $filePath, string $folder): string
    {
        $imagePathParts = explode('/', $filePath);
        $imageName = $imagePathParts[count($imagePathParts) - 1];
        $imagePathParts[count($imagePathParts) - 1] = $folder;

        $newFolderPath = implode('/', $imagePathParts);
        $this->checkNewFolder($newFolderPath);
        $imagePathParts[] = $imageName;
        return implode('/', $imagePathParts);
    }

    protected function checkNewFolder(string $path): void
    {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
    }
}
