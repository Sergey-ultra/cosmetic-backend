<?php

namespace App\Jobs;

use App\Models\Review;
use App\Models\Sku;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UpdateSkuRatingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public function __construct(
        protected Sku $currentSku,
        protected string $type,
    ){}

    public function handle(): void
    {
        $reviewCount = Review::query()->where('sku_id', $this->currentSku->id)->count();

        if ($this->type === 'plus') {
            $newCommonRating = ($this->currentSku->rating * $reviewCount + $this->currentSku->rating) / ($reviewCount + 1);
            $reviewsCount = $this->currentSku->reviews_count + 1;
        } else if ($this->type === 'minus') {
            $newCommonRating = ($this->currentSku->rating * $reviewCount - $this->currentSku->rating) / ($reviewCount - 1);
            $reviewsCount = $this->currentSku->reviews_count - 1;
        }


        $this->currentSku->rating = $newCommonRating;
        $this->currentSku->reviews_count = $reviewsCount;
        $this->currentSku->save();
    }
}
