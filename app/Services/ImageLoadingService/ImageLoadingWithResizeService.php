<?php

namespace App\Services\ImageLoadingService;

use App\Exceptions\ImageSavingException;
use App\Jobs\CompressImageJob;
use Illuminate\Support\Facades\Storage;
use Imagick;

class ImageLoadingWithResizeService
{
    const HEIGHT = 0.96;
    public function loadingImage(string $destinationFolder, string $sourceUrl, string $fileName): ImageSavedPathDTO
    {
        $destinationPath = $destinationFolder . $fileName;

        $filePath = Storage::path($destinationPath);
        $imageSavePath = Storage::url($destinationPath);


        try {
            if (! (is_file($filePath) && file_exists($filePath))) {
                $fileContent = file_get_contents($sourceUrl);
                $this->crop($fileContent, $filePath);

                CompressImageJob::dispatch($filePath);
            }

            $size = @getimagesize($filePath);

            return new ImageSavedPathDTO($size, $imageSavePath);
        } catch (\Throwable $e) {
            throw new ImageSavingException($e->getMessage());
        }
    }

    public function crop(string $imageBlob, string $filePath): void
    {

        $im = new Imagick();
        $im->readImageBlob($imageBlob);

        $imageWidth = $im->getImageWidth();
        $imageHeight = $im->getImageHeight();

        $im->cropImage($imageWidth, $imageHeight * self::HEIGHT, 0, 0);
        $im->setImageFormat("webp");
        $im->setOption('webp:method', '6');

        $this->checkNewFolder($this->getFolder($filePath));
        $im->writeImage($filePath);
    }

    protected function getFolder(string $filePath): string
    {
        $pathParts = explode('/', $filePath);
        unset($pathParts[count($pathParts) - 1]);

        return implode('/', $pathParts);
    }

    protected function checkNewFolder(string $path): void
    {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
    }
}
