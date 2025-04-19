<?php

declare(strict_types=1);


namespace App\Services\PriceHistoryService;




use App\Models\PriceHistory;
use Illuminate\Support\Facades\DB;

class PriceHistoryService implements PriceHistoryInterface
{

    public function makePriceDynamicsBySkuId(int $skuId, bool $sqlMode = true): array
    {
        if ($sqlMode) {
            return DB::table('price_histories')
                ->selectRaw("AVG(price) AS avg, MAX(price) AS max, MIN(price) AS min,  DATE(created_at) AS date")
                ->where('sku_id', $skuId)
                ->whereNotNull('price')
                ->groupBy(DB::raw("CONCAT(YEAR(created_at), '-', WEEK(created_at))"))
                ->orderBy(DB::raw('date'))
                ->get()
                ->toArray();
        }

        return $this->getPriceDynamics($skuId);
    }


    private function getPriceDynamics(int $skuId): array
    {
        $prices = PriceHistory::query()
            ->select('price', 'created_at')
            ->where('sku_id', $skuId)
            ->whereNotNull('price')
            ->orderBy('created_at')
            ->get()
            ->toArray();


        $minDate = $prices[0]['created_at'];
        $maxDate = $prices[count($prices) - 1]['created_at'];
        $rawMaxDate = strtotime($maxDate);

        $rawDate = strtotime('previous Monday', strtotime($minDate));

        $endPoints = [];
        while ($rawDate < $rawMaxDate) {
            $next = strtotime('next Monday',  $rawDate);

            $combinedPrices = [];
            foreach ($prices as  &$price) {
                $currentRawDate = strtotime($price['created_at']);
                if ($rawDate <= $currentRawDate && $currentRawDate < $next) {
                    $combinedPrices[] = $price['price'];
                    unset($price);
                }
            }

            if (count($combinedPrices)) {
                $endPoints[] = $this->makePoint($combinedPrices, $rawDate);
            }

            $rawDate = $next;
        }

        return $endPoints;
    }


    private function makePoint(array $prices, $rawDate): array
    {
        $sum = $maxPrice = $minPrice = $prices[0];

        for ($i = 1; $i < count($prices); $i++) {
            $sum += $prices[$i];

            if ($prices[$i] < $minPrice) {
                $minPrice = $prices[$i];
            }
            if ($prices[$i] > $maxPrice) {
                $maxPrice = $prices[$i];
            }
        }

        return [
            'min' => $minPrice,
            'max' => $maxPrice,
            'avg' => round($sum / count($prices), 2),
            'date' => date('Y.m.d', $rawDate)
        ];
    }
}
