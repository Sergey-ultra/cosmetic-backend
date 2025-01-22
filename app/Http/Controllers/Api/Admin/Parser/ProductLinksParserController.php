<?php

namespace App\Http\Controllers\Api\Admin\Parser;

use App\Http\Controllers\Controller;
use App\Models\LinkOption;
use App\Repositories\LinkRepository\ProductLinkRepository;
use App\Services\Parser\LinkCrawlerParser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductLinksParserController extends Controller
{
    public function parseLinks(Request $request, LinkCrawlerParser $linkParserService): JsonResponse
    {
        set_time_limit(7200);

        $storeId = (int)$request->store_id;
        $categoryId = (int)$request->category_id;
        $isLoadToDb = (bool)($request->isLoadToDb ?? false);


        $linkOption = LinkOption::query()
            ->select(
                'link_options.id',
                'link_options.options',
                'stores.link'
            )
            ->with('pages')
            ->join('stores', 'stores.id', '=', 'link_options.store_id')
            ->where(['link_options.store_id' => $storeId, 'link_options.category_id' => $categoryId])
            ->first();


        if (!$linkOption) {
            return response()->json(['data' => ['message' => 'нет настроек']]);
        }

        /** @var array{
         *     code: int,
         *     content: ?string
         * } $bodyArray
         */
        $bodyArray = $linkOption->pages
            ? $linkOption->pages
                ->map(fn(array $item) => json_decode($item['body'] ,true))
                ->all()
            : [];

//        $isRelatedProductLink =  (bool)($linkOption->options['relatedLink'] ?? false);
//        $isRelatedPageUrl = (bool)($linkOption->options['relatedPageUrl'] ?? false);

        $linkParserService
            ->setLinkOptionId($linkOption->id)
            ->setBody($bodyArray)
            ->setNextPage($linkOption->options['nextPage'] ?? null)
            ->setLink($linkOption->options['productLink'])
            ->setTargetUrl($linkOption->link);



        $parsedLinks = $linkParserService->parseLinksFromPage($linkOption->options['categoryUrl']);

        $result['links'] = $parsedLinks;

        if (count($parsedLinks) === 0) {
            $result['message'] = 'Нет спарсенных ссылок';
            $result['bodies'] = $linkParserService->getParsedBodies();

        } else {
            //$result['links'] = $parsedLinks;
            $result['message'] = 'success';

            if ($isLoadToDb) {
                (new ProductLinkRepository())->insert($parsedLinks, $storeId, $categoryId);
            }
        }

        return response()->json(['data' => $result]);
    }
}
