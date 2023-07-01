<?php

namespace App\Http\Controllers\Api\Admin\Parser;


use App\Configuration;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Parser\HourCountRequest;
use App\Http\Requests\ParsingLinkIdsRequest;
use App\Models\Link;
use App\Models\ParsingLink;
use App\Services\Parser\PriceCrawlerService;
use App\Services\Parser\PriceParsingByCronService;
use Illuminate\Http\JsonResponse;

class PriceParserController extends Controller
{
    public function maxLinkCountPerStore(PriceParsingByCronService $priceParsingByCronService): JsonResponse
    {
        return response()->json(['data' => $priceParsingByCronService->maxLinkCountPerStore()]);
    }

    public function parsePricesByLinkIds(ParsingLinkIdsRequest $request, PriceCrawlerService $priceParser): JsonResponse
    {
        $links = ParsingLink::query()
            ->select('parsing_links.link', 'price_options.options')
            ->join('price_options', 'price_options.store_id', '=','parsing_links.store_id')
            ->whereIn('parsing_links.id', $request->ids)
            ->get()
            ->toArray();

        $result = [];
        foreach ($links as $link) {
            $priceTag = json_decode($link['options'], true)['priceTag'];

            $result[] = $priceParser->parsePricesByLink($link['link'], $priceTag);
        }

        return response()->json(['data' => $result]);
    }

    public function parsePricesFromActualPriceParsingTable(PriceParsingByCronService $priceParsingByCronService): JsonResponse
    {
        $result = $priceParsingByCronService->handle();

        return response()->json(['data' => $result]);
    }

    public function getMinHourCount(Configuration $configuration): JsonResponse
    {
        return response()->json(['data' => $configuration->get('min_hour_count')]);
    }

    public function getMaxHourCount(Configuration $configuration): JsonResponse
    {
        return response()->json(['data' => $configuration->get('max_hour_count')]);
    }

    public function setMinHourCount(HourCountRequest $request, Configuration $configuration): JsonResponse
    {
        $configuration->set('min_hour_count', $request->post('hour_count'));
        return response()->json(['data' => ['status' => 'success']]);
    }

    public function setMaxHourCount(HourCountRequest $request, Configuration $configuration): JsonResponse
    {
        $configuration->set('max_hour_count', $request->post('hour_count'));
        return response()->json(['data' => ['status' => 'success']]);
    }
}
