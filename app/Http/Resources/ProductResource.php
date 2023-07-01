<?php

namespace App\Http\Resources;

use App\Models\Ingredient;
use App\Services\TransformImagePathService\TransformImagePathService;
use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\Pure;

class ProductResource extends JsonResource
{
    protected $skuPrices;

    #[Pure] public function __construct($resource, $skuPrices)
    {
        parent::__construct($resource);
        $this->skuPrices = $skuPrices;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $options = null;
        if ($this->application) {
            $options['application'] = $this->application;
        }
        if ($this->purpose) {
            $options['purpose'] = $this->purpose;
        }
        if ($this->effect) {
            $options['effect'] = $this->effect;
        }
        if ($this->age) {
            $options['age'] = $this->age;
        }
        if ($this->type_of_skin) {
            $this['type_of_skin'] = $this->type_of_skin;
        }

        $prices = $this->skuPrices->pluck('price')->all();
        $minPrice = !empty($prices) ? min($prices) : 0;
        $maxPrice = !empty($prices) ? max($prices) : 0;

        return [
            'id' => $this->sku_id,
            'volume' => $this->volume,
            'rating' => $this->rating,
            'images' => $this->images ? json_decode($this->images, true) : [],
            'reviews_count' => $this->reviews_count,
            'name' => $this->name,
            'code' => $this->code,
            'category_id' => $this->category_id,
            'category' => $this->category,
            'category_code' => $this->category_code,
            'brand' => $this->brand,
            'brand_code' => $this->brand_code,
            'brand_image' => $this->brand_image,
            'country' => $this->country,
            'description' => $this->description,
            'question_count' => 0,
            'options' => $options,
            'prices' => $this->skuPrices,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'ingredients' => $this->ingredients->pluck('name')->all(),
            'active_ingredients' => $this->ingredients
                ->filter(function($item) {
                return !empty($item->activeIds->all());
                 })
                ->values()
                ->map(function($item) {
                    return [
                      'id' => $item->id,
                      'name' => $item->name
                    ];
                })->all(),
            //'active_ingredients' => $this->ingredients,
            'skus' => SkuResource::collection($this->skus),
        ];
    }
}
