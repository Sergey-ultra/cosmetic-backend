<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImageRequest;
use App\Services\CompressImageService\CompressImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{


    public function store(ImageRequest $request, CompressImageService $compressImageService) {
        if ($request->has('images')) {

            $uploadedFiles = $request->images;
            $folder = $request->folder;
            $imagePreview = [];

            foreach($uploadedFiles as $file) {
                $fileName = $file->getClientOriginalName();

                $realFilePath = $file->storeAs('/public/image/' . $folder, $fileName);

                $imagePreview[] = Storage::url($realFilePath);
                $compressImageService->compress(Storage::path($realFilePath));
            }

            return response()->json(['data' => $imagePreview]);
        }
        return response()->json(['data' => [
            'status' => false
        ]]);
    }

    public function destroy($imageCode){

    }
}
