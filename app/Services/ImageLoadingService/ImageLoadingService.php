<?php

declare(strict_types=1);

namespace App\Services\ImageLoadingService;

use App\Exceptions\ImageSavingException;
use App\Jobs\CompressImageJob;
use Illuminate\Support\Facades\Storage;

class ImageLoadingService implements ImageLoadingInterface
{
    /**
     * @param string $destinationFolder
     * @param string $sourceUrl
     * @param string $fileName
     * @return ImageSavedPathDTO
     * @throws ImageSavingException
     */
    public function loadingImage(string $destinationFolder, string $sourceUrl, string $fileName): ImageSavedPathDTO
    {
        try {
            $imageName = $this->getFileName($sourceUrl, $fileName);

            $destinationPath = $destinationFolder . $imageName;


            $filePath = Storage::path($destinationPath);
            $imageSavePath = Storage::url($destinationPath);

            if (! (is_file($filePath) && file_exists($filePath)) ) {
                $fileContent = file_get_contents($sourceUrl);

                if ($fileContent) {
                    $isSavingSuccess = Storage::put($destinationPath, $fileContent);
                    if ($isSavingSuccess) {

                        //$size = file_put_contents($destinationPath, $fileContent);

                        //CompressImageJob::dispatch($imageSavePath);
                        CompressImageJob::dispatch($filePath);
                    }
                }
            } else  {
                //$size = exif_imagetype($filePath);
            }

            $size = @getimagesize($filePath);

            return new ImageSavedPathDTO($size, $imageSavePath);

        } catch (\Throwable $e) {
            throw new ImageSavingException($e->getMessage());
        }
    }


    /**
     * @param string $sourceUrl
     * @param string $fileName
     * @return string
     */
    protected function getFileName(string $sourceUrl, string $fileName = ''): string
    {
        $imageUrlParts = explode('/', $sourceUrl);
        $imageName = $imageUrlParts[count($imageUrlParts) - 1];
        if ($fileName !== "") {
            $fileName = preg_replace('#\*#', '_', $fileName);
            $fileName = preg_replace("#[/:*?\"<>|+%!@]#", '', $fileName);

            $imageNameParts = explode('.', $imageName);
            $extension = $imageNameParts[count($imageNameParts) - 1];
            $imageName = $fileName . '.' . $extension;
        }
        return $imageName;
    }
}
