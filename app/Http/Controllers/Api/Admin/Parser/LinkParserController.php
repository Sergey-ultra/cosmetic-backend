<?php

namespace App\Http\Controllers\Api\Admin\Parser;

use App\Http\Controllers\Controller;
use App\Models\LinkOption;
use App\Models\ParsingLink;
use App\Services\Parser\LinkCrawlerParser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LinkParserController extends Controller
{
    public function parseLinks(Request $request):JsonResponse
    {
        set_time_limit(7200);

        $storeId = (int)$request->store_id;
        $categoryId = (int)$request->category_id;
        $isLoadToDb = (bool)($request->isLoadToDb ?? false);



        $linkOption = LinkOption::select(
            'link_options.id',
            'link_options.options',
            'link_options.body',
            'stores.link'
        )
            ->join('stores', 'stores.id', '=', 'link_options.store_id')
            ->where(['link_options.store_id' => $storeId, 'link_options.category_id' => $categoryId])
            ->first();



        $linkParserService = new LinkCrawlerParser(
            $linkOption->id,
            $linkOption->body ?? [],
            $linkOption->options['nextPage'] ?? null,
            (bool) $linkOption->options['relatedLink'],
            $linkOption->options['productLink'],
            (bool) $linkOption->options['relatedPageUrl'],
            $linkOption->link
        );


        $parsedLinks = $linkParserService->parseProductLinks($linkOption->options['categoryUrl']);

        if (count($parsedLinks) === 0) {
            $result =  [
                'message' => 'Нет спарсенных ссылок',
                'res' => $parsedLinks
            ];

        } else {
            //$result['links'] = $parsedLinks;
            $result['links'] = $parsedLinks;

            if ($isLoadToDb) {
                $parsingLinks = array_map(
                    function ($link) use ($storeId, $categoryId) {
                        return [
                            "link" => $link,
                            "parsed" => 0,
                            "store_id" => $storeId,
                            "category_id" => $categoryId
                        ];
                    },
                    $parsedLinks
                );

                ParsingLink::insert($parsingLinks);

                $result['message'] = 'success';
            }
        }

        return response()->json(['data' => $result]);
    }
}
