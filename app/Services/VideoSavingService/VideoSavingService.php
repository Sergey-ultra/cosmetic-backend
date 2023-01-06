<?php


namespace App\Services\VideoSavingService;


use Illuminate\Support\Facades\Storage;

class VideoSavingService implements VideoSavingInterface
{
    public function saveOneFile(string $stringData, string $folder, string $fileName): string
    {

        if (preg_match('/^data:video\/(.*);base64,/', $stringData) || preg_match('/;base64,/', $stringData)) {
            //$type = mime_content_type($stringData);

            $extension = explode('/', explode(':', substr($stringData, 0, strpos($stringData, ';')))[1])[1];

            $extension = str_replace('+xml', '', $extension);

            $replace = substr($stringData, 0, strpos($stringData, ',') + 1);

            $video = str_replace($replace, '', $stringData);
            $video = str_replace(' ', '+', $video);

            $destinationPath = $folder . $fileName . '.' . $extension;

            Storage::put($destinationPath, base64_decode($video));
            return Storage::url($destinationPath);
        }

       return $stringData;
    }
}
