<?php


namespace App\Services\VideoSavingService;


use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class VideoSavingService implements VideoSavingInterface
{
    public function saveOneAsFile(string $stringData, string $folder, string $fileName): array
    {

    }

    public function saveOneAsBase64(string $stringData, string $folder, string $fileName): array
    {

        if (preg_match('/^data:video\/(.*);base64,/', $stringData) || preg_match('/;base64,/', $stringData)) {


            $replace = substr($stringData, 0, strpos($stringData, ',') + 1);

            $video = str_replace($replace, '', $stringData);
            $video = str_replace(' ', '+', $video);

            $videoDestinationPath = $folder . $fileName . '.' . $this->getExtension($stringData);
            Storage::put($videoDestinationPath, base64_decode($video));
            $videoFilePath = Storage::url($videoDestinationPath);

            $thumbnailPath = $this->saveThumbnailByFilepath($videoFilePath, $folder, $fileName);

            return [$videoFilePath, Storage::url($thumbnailPath)];
        }

       return [$stringData, null];
    }


    public function saveThumbnailByFilepath(string $videoFilePath, string $folder, string $fileName): ?string
    {
        $thumbnailPath = "$folder/thumbnail/$fileName.jpg";
        try {
            FFMpeg::fromDisk('public')
                ->open(str_replace('/storage', '', $videoFilePath))
                ->getFrameFromSeconds(2)
                ->export()
                ->toDisk('local')
                ->save($thumbnailPath);

            return $thumbnailPath;
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function getExtension(string $stringData): string
    {
        $type = mime_content_type($stringData);
        $extension = explode('/', $type)[1];
        $extension =  str_replace('+xml', '', $extension);

        if (str_contains($extension, '-')) {
            $extension = explode('-', $extension)[1];
        }

        return $extension;
    }
}
