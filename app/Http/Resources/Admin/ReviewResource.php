<?php

declare(strict_types=1);


namespace App\Http\Resources\Admin;


use Illuminate\Http\Resources\Json\JsonResource;

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
            'product_name' => $this->name,
            'product_link' => "/product/{$this->code}-{$this->sku_id}",
            'rating' => $this->rating,
            'user' => $this->user,
            'review_id' => $this->review_id,
            'comment' => $this->comment ? substr($this->comment, 0, 100): null,
            'minus' => $this->minus ? substr($this->minus, 0, 100): null,
            'plus' =>  $this->plus ? substr($this->plus, 0, 100): null,
            'anonymously' => $this->anonymously ?? null,
            //'images' => $this->images ? json_decode($this->images, true) : null,
            'images' => $this->images ? json_decode($this->images, true):  [],
            'review_status' => $this->review_status
        ];
    }
}