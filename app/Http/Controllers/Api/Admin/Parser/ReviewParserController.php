<?php

namespace App\Http\Controllers\Api\Admin\Parser;

use App\Http\Controllers\Controller;
use App\Models\LinkOption;
use App\Models\ReviewLinkOption;
use App\Models\ReviewLinkPage;
use App\Repositories\LinkRepository\ReviewLinkRepository;
use App\Services\Parser\LinkCrawlerParser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewParserController extends Controller
{
    const TARGET_LINK = 'https://kosmetista.ru';

    public function linkOptions(Request $request): JsonResponse
    {
        $result = ReviewLinkOption::query()->where(['category_id' => $request->input('category_id')])->first();

        return response()->json(['data' => $result?->options]);
    }

    public function updateOrCreate(Request $request): JsonResponse
    {
        $newOption = ReviewLinkOption::updateOrCreate(
            ['category_id' => $request->input('category_id')],
            ['options' => $request->input('options')]
        );

        return response()->json(['data' => $newOption]);
    }

    public function parseLinks(
        Request $request,
        LinkCrawlerParser $linkParserService,
        ReviewLinkRepository $reviewLinkRepository
    ): JsonResponse
    {
        set_time_limit(7200);

        $categoryId = $request->input('category_id');
        $isLoadToDb = (bool)($request->isLoadToDb ?? false);

        $linkOption = ReviewLinkOption::query()
            ->select('id', 'options')
            ->with('pages')
            ->where( 'category_id', $categoryId)
            ->first();

        if (!$linkOption) {
            return response()->json(['data' => ['message' => 'нет настроек']]);
        }

        $bodyArray = $linkOption->pages
            ? $linkOption->pages
                ->mapWithKeys(fn(ReviewLinkPage $item) => [$item['page_number'] => json_decode($item['body'] ,true)])
                ->all()
            : [];


        $linkParserService
            ->setStartPageNumber($linkOption->options['startPageNumber'])
            ->setEndPageNumber($linkOption->options['endPageNumber'])
            ->setLinkOptionId($linkOption->id)
            ->setBody($bodyArray)
            ->setNextPage($linkOption->options['nextPage'] ?? null)
            ->setLink($linkOption->options['link'])
            ->setTargetUrl(self::TARGET_LINK)
            ->setPaginationQueryString($linkOption->options['paginationQuery'])
            ->setLinkPageRepository($reviewLinkRepository);

        $parsedLinks = $linkParserService->parseLinksFromPage($linkOption->options['categoryUrl']);
        $result['links'] = $parsedLinks;

        if (count($parsedLinks) === 0) {
            $result['message'] = 'Нет спарсенных ссылок';
            $result['bodies'] = $linkParserService->getParsedBodies();

        } else {
            $result['message'] = 'success';

            if ($isLoadToDb) {
                $reviewLinkRepository->insert($parsedLinks, $categoryId);
            }
        }

        return response()->json(['data' => $result]);
    }
}
