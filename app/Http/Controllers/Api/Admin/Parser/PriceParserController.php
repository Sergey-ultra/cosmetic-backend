<?php

namespace App\Http\Controllers\Api\Admin\Parser;


use App\Http\Controllers\Controller;
use App\Http\Requests\ParsingLinkIdsRequest;
use App\Models\ParsingLink;
use App\Services\Parser\PriceCrawlerService;
use App\Services\Parser\PriceParsingByCronService;
use Illuminate\Http\JsonResponse;

class PriceParserController extends Controller
{
    public function parsePricesByLinkIds(ParsingLinkIdsRequest $request, PriceCrawlerService $priceParser): JsonResponse
    {
        $links = ParsingLink::select('parsing_links.link', 'price_options.options')
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
}
