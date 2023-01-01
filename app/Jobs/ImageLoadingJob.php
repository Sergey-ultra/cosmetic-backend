<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\CompressImageService\CompressImageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Imagick;

class ImageLoadingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        protected  string $sourcePath,
        protected  string $destinationFolder,
        protected  string $imageName
    ){}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(CompressImageService $compressImageService)
    {
        $fileContent = file_get_contents($this->sourcePath);
        $destinationPath = $this->destinationFolder . $this->imageName;

        Storage::put($destinationPath, $fileContent);
        try {
            $compressImageService->compress(Storage::path($destinationPath));
        } catch (\ImagickException $e) {

        }
    }
}
