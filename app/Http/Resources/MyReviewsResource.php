<?php

declare(strict_types=1);


namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class MyReviewsResource extends JsonResource
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
            'rating' => $this->rating,
            'sku_name' => $this->sku_name,
            'product_code' => $this->product_code,
            'sku_id' => $this->sku_id,
            'volume' => $this->volume,
            'sku_image' => $this->sku_images ? json_decode($this->sku_images, true)[0] : [],
            'review_id' => $this->review_id,
            'comment' => $this->comment,
            'plus' => $this->plus,
            'minus' => $this->minus,
            'images' => $this->review_images ? json_decode($this->review_images,true) : [],
            'status' => $this->status,
            'created_at' => $this->created_at ? $this->created_at->toDateString() : null,
            'user_name' =>
                isset($this->review_id)
                    ? ($this->anonymously === 0 ? $this->user_name : 'Имя скрыто')
                    : $this->user_name,
            'user_avatar' =>
                isset($this->review_id)
                    ? ($this->anonymously === 0 ? $this->avatar : '/storage/icons/user_avatar.png')
                    : $this->avatar
        ];
    }
}