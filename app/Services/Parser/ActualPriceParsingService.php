<?php

declare(strict_types=1);

namespace App\Services\Parser;


use App\Models\ActualPriceParsing;
use App\Models\PriceHistory;

class ActualPriceParsingService
{

    public function index(int $count): array
    {
        return array_map(
            function($el) {
                $el['links_by_time'] = json_decode($el['links_by_time'], true);
                return $el;
            },
            ActualPriceParsing::select('id', 'links_by_time')->limit($count)->get()->toArray()
        );
    }



    public function copyLinksToActualPriceParsingTable(): void
    {
        $linksWithSkuIdsAndStoreIds =
            PriceHistory::select(
                'price_histories.sku_id',
                'price_histories.store_id',
                'price_histories.link_id',
                'links.link',
                'price_options.options')
                ->join('price_options', 'price_histories.store_id', '=', 'price_options.store_id')
                ->join('links', 'price_histories.link_id', '=', 'links.id')
                ->join('stores', 'price_histories.store_id', '=', 'stores.id')
                ->where('stores.price_parsing_status', 1)
                ->groupBy('price_histories.link_id')
                ->get()
                ->toArray();



        $linksWithSkuIdsAndStoreIds = array_map(
            function($el) {
                return ['links_by_time' => json_encode($el)];
            },
            $this->linkSortByStoreService($linksWithSkuIdsAndStoreIds)
        );

        ActualPriceParsing::insert($linksWithSkuIdsAndStoreIds);
    }


    protected function linkSortByStoreService(array &$links): array
    {
        $groups = [];
        foreach($links as $key => $link) {
            $link['priceTag'] =  json_decode($link['options'], true)['priceTag'];
            unset($link['options']);
            $groups[$link['store_id']][] = $link;
            unset($links[$key]);
        }

        $result = [];
        while (count(array_keys($groups)) > 1) {
            $temp = [];
            foreach (array_keys($groups) as $key) {
                if (count($groups[$key]) > 0) {
                    $temp[] = $groups[$key][count($groups[$key]) - 1];
                    unset($groups[$key][count($groups[$key]) - 1]);
                } else {
                    unset($groups[$key]);
                }
            }
            $result[] = $temp;
        }


        foreach($groups[array_keys($groups)[0]] as $lastElement) {
            $result[] = [$lastElement];
        }
        return $result;
    }
}
