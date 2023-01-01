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

class CompressImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(protected string $path)
    {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(CompressImageService $compressImageService)
    {
        try {
            $compressImageService->compress($this->path);
        } catch (\Throwable $e) {

        }
    }
}
