<?php

declare(strict_types=1);


namespace App\Services\PriceHistoryService;




class PriceHistoryService implements PriceHistoryInterface
{

    public function makePriceDynamics(array $prices): array
    {
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