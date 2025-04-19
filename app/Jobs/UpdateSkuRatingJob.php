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
        protected Review $review,
        protected string $type,
    ){}

    public function handle(): void
    {
        $currentSku = $this->review->sku;
        //$reviewCount = Review::query()->where('sku_id', $currentSku->id)->count();

        if ($this->type === 'plus') {
            $newCommonRating = ($currentSku->rating * $currentSku->reviews_count + $this->review->rating) / ($currentSku->reviews_count + 1);
            $reviewsCount = $currentSku->reviews_count + 1;
        } else if ($this->type === 'minus') {
            if ($currentSku->reviews_count - 1 === 0) {
                $reviewsCount = 0;
                $newCommonRating = 5;
            } else {
                $newCommonRating = ($currentSku->rating * $currentSku->reviews_count - $this->review->rating) / ($currentSku->reviews_count - 1);
                $reviewsCount = $currentSku->reviews_count - 1;
            }
        }


        $currentSku->rating = $newCommonRating;
        $currentSku->reviews_count = $reviewsCount;
        $currentSku->save();
    }
}
