<?php

declare(strict_types=1);


namespace App\Http\Resources\Admin;


use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'id' => $this->id,
            'category' => $this->category,
            'brand' => $this->brand,
            'name' => $this->name,
            'code' => $this->code,
            'url' => sprintf('%s-%s', $this->code, $this->id),
            'is_ingredients_exist' => $this->is_ingredients_exist,
            'volume' => $this->volume,
            'images' => $this->images
//                ? json_decode($this->images, true) : null
            ,
            'created_at' => $this->created_at->format('Y-m-d'),
            'user_name' => $this->user_name,
            'status' => $this->status,
            'link_count' => $this->link_count
        ];
    }
}
