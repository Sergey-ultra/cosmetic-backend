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
            'volume' => $this->volume,
            'images' => $this->images ? json_decode($this->images, true) : null,
            'created_at' => substr($this->created_at, 0, 10)
        ];
    }
}