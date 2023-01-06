<?php


namespace App\Services\VideoSavingService;


use Illuminate\Support\Facades\Storage;

class VideoSavingService implements VideoSavingInterface
{
    public function saveOneFile(string $stringData, string $folder, string $fileName): string
    {

        if (preg_match('/^data:video\/(.*);base64,/', $stringData) || preg_match('/;base64,/', $stringData)) {


            $replace = substr($stringData, 0, strpos($stringData, ',') + 1);

            $video = str_replace($replace, '', $stringData);
            $video = str_replace(' ', '+', $video);

            $destinationPath = $folder . $fileName . '.' . $this->getExtension($stringData);

            Storage::put($destinationPath, base64_decode($video));
            return Storage::url($destinationPath);
        }

       return $stringData;
    }

    protected function getExtension(string $stringData): string
    {
        $type = mime_content_type($stringData);

        $extension = explode('-', explode('/', $type)[1])[1];

        return str_replace('+xml', '', $extension);
    }
}
