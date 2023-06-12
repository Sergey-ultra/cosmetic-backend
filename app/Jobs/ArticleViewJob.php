<?php

namespace App\Jobs;

use App\Models\ArticleView;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ArticleViewJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected int $articleId, protected string $ipAddress){}

    public function handle(): void
    {
        try {
            ArticleView::query()->updateOrCreate([
                'article_id' => $this->articleId,
                'ip_address' => $this->ipAddress,
            ], []);
        } catch (\Throwable $e) {
            echo $e->getMessage();
        }
    }
}
