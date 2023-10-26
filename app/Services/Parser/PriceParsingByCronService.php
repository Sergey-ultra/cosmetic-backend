<?php

declare(strict_types=1);

namespace App\Services\Parser;


use App\Configuration;
use App\Exceptions\BulkInsertException;
use App\Exceptions\CurrentPriceInsertDataException;
use App\Models\ActualPriceParsing;
use App\Models\Link;
use DateTime;

class PriceParsingByCronService
{
    public const MIN_HOUR_COUNT = 10;
    public const MAX_HOUR_COUNT = 17;
    public const ATTEMPTS_PER_WEEK = 100;
    public function __construct(
        protected ActualPriceParsingService $actualPriceParsingService,
        protected Configuration $configuration,
        protected PriceBulkInsertService $priceBulkInsertService,
        protected PriceCrawlerService $priceParser
    ) {}

    public function handle(): string
    {
       $res = 'start' . (new \DateTime())->format('Y-m-d H:i:s');

        try {
            if ($this->configuration->getBoolean('week_status')) {
                if (! ($minHourCount = (int)$this->configuration->get('min_hour_count'))) {
                    $minHourCount = self::MIN_HOUR_COUNT;
                }

                if (! ($maxHourCount = (int)$this->configuration->get('max_hour_count'))) {
                    $maxHourCount = self::MAX_HOUR_COUNT;
                }

                $linksWithSkuIdsAndStoreIds = $this->actualPriceParsingService->index(rand($minHourCount, $maxHourCount));

                $linesCount = $linksWithSkuIdsAndStoreIds->count();


                if ($linesCount === 0) {
                    $this->fillActualPriceParsingTable();
                } else {
                    $start = microtime(true);
                    set_time_limit(7200);

                    $parsedPrices = [];
                    $parsedActualIds = [];
                    $parsedLinkIdsString = '';

                    foreach ($linksWithSkuIdsAndStoreIds as $line) {
                        $parsedActualIds[] = $line->id;

                        $this->priceParser->setRequestsCountBeforeChangingProxy(count($line->links_by_time));
                        foreach ($line->links_by_time as $link) {
                            try {
                                [$status, $price] = $this->priceParser->parsePricesByLink($link['link'], $link['priceTag']);
                                $parsedLinkIdsString .= "Ссылка с id  {$link['link_id']} статус $status store_id - {$link['store_id']}\r\n";

                                if (in_array($status, [200, 301, 404])) {
                                    $parsedPrices[] = [
                                        'sku_id' => $link['sku_id'],
                                        'store_id' => $link['store_id'],
                                        'link_id' => $link['link_id'],
                                        'price' => $price !== 0 ? $price : NULL
                                    ];
                                }

                            } catch (\Throwable $e) {
                                $parsedLinkIdsString .= "Ссылка с id {$link['link_id']} произошла ошибка " . $e->getMessage()
                                    . $e->getFile() . $e->getLine() . "\r\n";
                            }
                        }

                        if (
                            count($parsedPrices) >= 10
                            && $this->priceBulkInsertService->priceBulkInsert($parsedPrices)
                        ) {
                            $parsedPrices = [];
                            ActualPriceParsing::whereIn('id', $parsedActualIds)->delete();
                            $parsedActualIds = [];


                            $res .= $parsedLinkIdsString;
                            $parsedLinkIdsString = '';
                        }

                        sleep(rand(3, 19));
                    }


                    if (
                        count($parsedPrices)
                        && $this->priceBulkInsertService->priceBulkInsert($parsedPrices)
                    ) {
                        ActualPriceParsing::whereIn('id', $parsedActualIds)->delete();
                        $res .= $parsedLinkIdsString;
                    }


                    $end = microtime(true);
                    $duration = round($end - $start, 2);
                    $res .= "Успешно обработано $linesCount строк из таблицы actual_price_parsing за $duration секунд";
                }




            } else {
                $res .= 'Недельный флаг в положении false';
            }
        } catch (CurrentPriceInsertDataException $er) {
            $res .=  "\r\n" . 'Ошибка при вставке в таблицу current_price' . $er->getMessage();
        } catch (BulkInsertException $err) {
            $res .=  "\r\n" . 'Ошибка при вставке в таблицу price_history' . $err->getMessage();
        } catch (\Throwable $e) {
            $res .=  "\r\n" . 'Ошибка произошла' . $e;
        } finally {
            $currentTime = (new DateTime())->format('Y-m-d H:i:s');

            $res .= "\r\n" . 'end' . $currentTime;

            Logger::write($res);
            return $res;
        }
    }

    protected function fillActualPriceParsingTable(): void
    {
        $this->actualPriceParsingService->copyLinksToActualPriceParsingTable();
        $this->configuration->setBoolean('week_status', false);
        $this->setMinAndMaxHourCounts();
    }

    protected function setMinAndMaxHourCounts(): void
    {
        $maxLinkCount = $this->maxLinkCountPerStore();
        $averageLinkCountPerAttempt = ceil($maxLinkCount / self::ATTEMPTS_PER_WEEK);

        $minHourCount = floor($averageLinkCountPerAttempt * 0.9);
        $maxHourCount = ceil($averageLinkCountPerAttempt * 1.55);

        $this->configuration->set('min_hour_count', $minHourCount);
        $this->configuration->set('max_hour_count', $maxHourCount);
    }

    public function maxLinkCountPerStore(): int
    {
        $sub = Link::query()
            ->selectRaw('count(store_id) AS count')
            ->groupBy('store_id');

        $result = Link::query()->from($sub, 'counts')->selectRaw('max(count) as maxCount')->first();

        return $result->maxCount;
    }
}
