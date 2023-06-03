<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

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
            'rating' => $this->rating,
            'user' => $this->user,
            'review_id' => $this->review_id,
            'body' => $this->body
                ? Str::substr($this->body, 0, 100)
                : null
            ,
            'minus' => $this->minus
                ? Str::substr($this->minus, 0, 100)
                : null,
            'plus' =>  $this->plus
                ? Str::substr($this->plus, 0, 100)
                : null,
            'anonymously' => $this->anonymously ?? null,
            'images' => $this->images
                ? json_decode($this->images, true)
                :  [],
            'review_status' => $this->review_status,
        ];
    }
}
