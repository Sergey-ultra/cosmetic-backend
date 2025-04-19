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
        'sku' => 'sku',
        'article' => 'articles',
        'brand' => 'brand',
        'article-ckeditor' => 'articles/ckEditor',
        'avatar' => 'avatar',

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


        if ($request->input('type') ==='image') {
            $savedFolder = '/public/image/' . self::ENTITY_MAP_FOLDER[$request->input('entity')];
        } else if ($request->input('type') ==='video') {
            $savedFolder = '/public/video/' . self::ENTITY_MAP_FOLDER[$request->input('entity')];
        }

        $savedName = $request->input('file_name');

        $savedFiles = [];


        foreach ($request->file('files') as $file) {
            if (!$savedName) {
                $savedName = $file->getClientOriginalName();
            }

            $savedName = $file->hashName(pathinfo($savedName, PATHINFO_FILENAME));

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


                if ($request->input('type') ==='image') {
                    $result = $compressImageService->compress(Storage::path($realFilePath));
                    if (!$result['status']) {
                        $savedFile['options'] = [
                            'is_compress_success' => false,
                            'is_compress_message' => $result['message']
                        ];
                    }
                } else if ($request->input('type') ==='video') {
                    $savedFile['thumbnail'] = $videoSavingService->saveThumbnailByFilepath(
                        $savedFilePath,
                        $request->input('folder'),
                        $request->input('file_name')
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
