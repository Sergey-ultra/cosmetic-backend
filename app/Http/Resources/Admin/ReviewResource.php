<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'sku_rating_id' => $this->sku_rating_id,
            'rating_status' => $this->rating_status,
            'product_name' => $this->sku_name,
            'product_link' => "/product/{$this->product_code}-{$this->sku_id}",
            'sku_image' => $this->sku_images
                ? json_decode($this->sku_images, true)[0]
                : null,
            'rating' => $this->rating,
            'views_count' => $this->views_count,
            'likes_count' => $this->likes_count,
            'user' => $this->user_name,
            'review_id' => $this->review_id,
            'title' => $this->title,
            'anonymously' => $this->anonymously ?? null,
            'images' => $this->images
                ? json_decode($this->images, true)
                : [],
            'review_status' => $this->review_status,
            'created_at' => (new Carbon($this->created_at))->format('Y-m-d'),
        ];
    }
}
