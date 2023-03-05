<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FileRequest;
use App\Services\CompressImageService\CompressImageInterface;
use App\Services\CompressImageService\CompressImageService;
use App\Services\VideoSavingService\VideoSavingInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\JsonResponse;

class FileController extends Controller
{
    public const ENTITY_MAP_FOLDER = [
        'review' => 'premoderatedReviews',
        'sku' => 'sku'
    ];
    /**
     * @param FileRequest $request
     * @param CompressImageInterface $compressImageService
     * @param VideoSavingInterface $videoSavingService
     * @return JsonResponse
     */
    public function storeAsForm(
        FileRequest $request,
        CompressImageInterface $compressImageService,
        VideoSavingInterface $videoSavingService,
    ): JsonResponse
    {
//            image|mimes:jpeg,png,jpg,gif,svg


        if ($request->type ==='image') {
            $savedFolder = '/public/image/' . self::ENTITY_MAP_FOLDER[$request->entity];
        } else if ($request->type ==='video') {
            $savedFolder = '/public/video/' . self::ENTITY_MAP_FOLDER[$request->entity];
        }

        $savedName = $request->file_name;

        $savedFiles = [];


        foreach ($request->file('files') as $file) {

            $originalName = $file->getClientOriginalName();

            if (!$savedName) {
                $savedName = $originalName;
            }

            $realFilePath = $file->storeAs($savedFolder, $savedName);

            $savedFile['saved_name'] = $savedName;

            if (false === $realFilePath) {
                $savedFile['status'] = false;
            } else {
                $savedFilePath = Storage::url($realFilePath);

                $savedFile = [
                    'status' => true,
                    'url' => $savedFilePath
                ];


                if ($request->type ==='image') {
                    $result = $compressImageService->compress(Storage::path($realFilePath));
                    if (!$result['status']) {
                        $savedFile['options'] = [
                            'is_compress_success' => false,
                            'is_compress_message' => $result['message']
                        ];
                    }
                } else if ($request->type ==='video') {
                    $savedFile['thumbnail'] = $videoSavingService->saveThumbnailByFilepath(
                        $savedFilePath,
                        $request->folder,
                        $request->file_name
                    );
                }
            }

            $savedFiles[] = $savedFile;
        }

        return response()->json(['data' => $savedFiles]);

    }

    public function destroy($imageCode)
    {

    }
}
