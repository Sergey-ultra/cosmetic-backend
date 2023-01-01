<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PriceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $min = 0;
        $max = 0;

        //       "SELECT
//          sku_store.*
//        FROM
//          (SELECT
//              link_id, price,MAX(created_at) AS created_at
//            FROM
//              sku_store
//            Where sku_id=2
//            GROUP BY
//              link_id) AS latest
//         INNER JOIN
//           sku_store
//         ON
//          sku_store.link_id = latest.link_id AND
//          sku_store.created_at = latest.created_at"

//        $latestPrices = DB::table('sku_store')
//            ->select(  'link_id', DB::raw('MAX(created_at) as created_at'))
//            ->where('sku_id', $this->currentSkuId)
//            ->groupBy('link_id');
//
//        $prices = DB::table('sku_store')
//            ->select('sku_store.link_id', 'sku_store.price', 'sku_store.created_at')
//            ->joinSub($latestPrices, 'latest', function ($join) {
//                $join->on('sku_store.link_id', '=', 'latest.link_id')
//                    ->on('sku_store.created_at', '=', 'latest.created_at');
//            })->get();

        $prices = [];
        foreach($this as $key => $store) {
            $price['price'] = $store->pivot->price;
            $price['created_at'] = $store->pivot->created_at;
            $price['name'] = $store->name;
            $price['link'] = $store->pivot->link->link;
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
        return $result;
    }
}
