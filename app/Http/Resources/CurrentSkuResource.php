<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CurrentSkuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $min = $max = 0;
        $prices = [];
        $stores = $this->stores;
        foreach($stores as  $store) {
            $price['price'] = $store->pivot->price;
            $price['created_at'] = $store->pivot->created_at;
            $price['name'] = $store->name;
            $price['link'] = $store->pivot->link->link;

            if (count($stores)) {
                $price['image'] = $store->image;
            }

            $prices[] = $price;
        }
        $prices = collect($prices)->sortByDesc('created_at')->groupBy('name')->values();

        $result = [];
        foreach($prices as $key => $price) {
            $result[] = $price[0];
            $currentPrice = $price[0]['price'];
            if ($key === 0) {
                $min = $max = $currentPrice;
            } else {
                if ($currentPrice < $min) {
                    $min =  $currentPrice;
                }
                if ($currentPrice > $max) {
                    $max =  $currentPrice;
                }
            }
        }
        return [
            'id' => $this->id,
            'image' => $this->images->firstWhere('isMain', 1)['image'],
            'price' => $result,
            'minPrice' => $min,
            'maxPrice' => $max
        ];
    }
}
