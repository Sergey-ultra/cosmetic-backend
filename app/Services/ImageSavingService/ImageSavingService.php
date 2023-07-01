<?php

declare(strict_types=1);

namespace App\Services\ImageSavingService;


use App\Services\CompressImageService\CompressImageService;
use Illuminate\Support\Facades\Storage;

class ImageSavingService implements ImageSavingInterface
{
    protected array $compressionOptions = [
            [
                'folder' => 'small',
                'width' => 200,
                'height' => 200
            ],
            [
                'folder' => 'medium',
                'width' => 500,
                'height' => 500,
            ]
        ];

    public function __construct(protected CompressImageService $compressImageService)
    {}

    public function saveImages(array $images, string $folder, string $fileName, bool $isCompress = true, array $options = []): array {
        $imageUrls = [];
        if ($isCompress && !count($options)) {
            $options = $this->compressionOptions;
        }

        foreach ($images as $key => $stringData) {
            $imageUrls[] = $this->saveOneImage($stringData, $folder, $fileName . '_' . ($key + 1), $isCompress, $options);
        }
        return $imageUrls;
    }

    public function saveOneImage(string $stringData, string $folder, string $fileName, bool $isCompress = true, array $options = []): string
    {
        if (preg_match('/^data:image\/(.*);base64,/', substr($stringData, 0, 100))) {

            $extension = explode('/', explode(':', substr($stringData, 0, strpos($stringData, ';')))[1])[1];

            $extension = str_replace('+xml', '', $extension);

            $replace = substr($stringData, 0, strpos($stringData, ',') + 1);

            $image = str_replace($replace, '', $stringData);
            $image = str_replace(' ', '+', $image);

            $destinationPath = $folder . $fileName . '.' . $extension;

            Storage::put($destinationPath, base64_decode($image));

            if ($extension !== 'svg' && $isCompress) {
                if (!count($options)) {
                    $options = $this->compressionOptions;
                }
                $this->compressImageService->compress(Storage::path($destinationPath), $options);
            }
            return Storage::url($destinationPath);
        }

        return $stringData;
    }
}
