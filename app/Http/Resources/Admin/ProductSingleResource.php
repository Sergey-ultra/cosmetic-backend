<?php

declare(strict_types=1);


namespace App\Http\Resources\Admin;


use Illuminate\Http\Resources\Json\JsonResource;

class ProductSingleResource  extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $ingredientIds = [];
        if ($this->tagIds) {
            $ingredientIds = $this->ingredients->map(function ($item, $key) {
                return $item->pivot->ingredient_id;
            });
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'brand_id' => $this->brand_id,
            'category_id' => $this->category_id,
            'description' => $this->description,
            'volume' => $this->volume,
            'images' =>  json_decode($this->images, true),
            'ingredients' => $this->ingredients,
            'ingredient_ids' => $ingredientIds
        ];
    }
}
