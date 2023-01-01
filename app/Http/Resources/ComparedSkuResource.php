<?php
declare(strict_types=1);


namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class ComparedSkuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $product = $this->product;
        $brand = $product->brand;
        $country = $brand->country;


        return [
            'id' => $this->id,
            'image' =>  json_decode($this->images, true)[0],
            'name' => $product->name,
            'code' => $product->code,
            'rating' => number_format((float) $this->rating, 1, '.', ''),
            'reviews_count' => $this->reviews_count,
            'volume' => $this->volume,
            'min_price' => $this->prices->min('price'),
            'prices_count' => $this->prices->count(),
            'brand' => $brand->name,
            'country' => $country ? $country->name : NULL,
            'ingredients' => $product->ingredients->pluck('name'),
        ];
    }
}