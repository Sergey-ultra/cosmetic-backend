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
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'product_name' => $this->sku_name,
            'product_link' => "/product/{$this->product_code}-{$this->sku_id}",
            'sku_image' => $this->sku_images
                ? json_decode($this->sku_images, true)[0]
                : null,
            'sku_status' => $this->sku_status,
            'rating' => $this->rating,
            'views_count' => $this->views_count,
            'likes_count' => $this->likes_count,
            'user' => $this->user_name,
            'title' => $this->title,
            'review_status' => $this->review_status,
            'created_at' => (new Carbon($this->created_at))->format('Y-m-d'),
        ];
    }
}
