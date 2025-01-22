<?php

namespace App\Http\Controllers\Api\Admin\Parser;

use App\Helpers\MemoryUsage;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\DataProviderWithDTO;
use App\Http\Controllers\Traits\ParamsDTO;
use App\Http\Requests\Admin\ReviewParsingRequest;
use App\Http\Resources\Admin\Parser\ParsedLinkCollection;
use App\Models\LinkOption;
use App\Models\ReviewLinkOption;
use App\Models\ReviewLinkPage;
use App\Models\ReviewParsingLink;
use App\Repositories\LinkRepository\ReviewLinkRepository;
use App\Services\Parser\LinkCrawlerParser;
use App\Services\Parser\ReviewParsingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewParserController extends Controller
{
    use DataProviderWithDTO;
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

    public function links(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 30);

        $query = ReviewParsingLink::query()
            ->select([
                'id',
                'link',
                DB::raw('DATE(created_at) as date'),
                DB::raw("IF(body IS NULL, false, true) AS is_body_exist")
            ])
            ->where('status', 0);

        $paramsDto = new ParamsDTO(
            $request->input('filter') ?? [],
            $request->input('sort') ?? '',
        );

        $result = $this->prepareModel($paramsDto, $query)->paginate($perPage);

        return response()->json(['data' => $result]);
    }

    public function parsedLinks(Request $request): ParsedLinkCollection
    {
        $perPage = $request->integer('per_page', 30);

        $query = ReviewParsingLink::query()
            ->select(['id', 'link', 'content', DB::raw('DATE(created_at) as date')])
            ->where('status', 1);

        $paramsDto = new ParamsDTO(
            $request->input('filter', []),
            $request->input('sort', ''),
        );

        $result = $this->prepareModel($paramsDto, $query)->paginate($perPage);
        return new ParsedLinkCollection($result);
    }

    public function showParsedLink(int $id): JsonResponse
    {
        $review = ReviewParsingLink::query()
            ->select(['id', 'link', 'content', 'category_id'])
            ->find($id);

        $content = json_decode($review->content, true);


        return response()->json(['data' => [
            'id' => $review->id,
            'category_id' => $review->category_id,
            'link' => $review->link,
            'title' => $content['title'] ?? '',
            'body' => $content['body'],
        ]]);
    }

    public function setPublished(int $id): JsonResponse
    {
        $review = ReviewParsingLink::query()
            ->find($id)
            ->update(['status' => ReviewParsingLink::PUBLISHED]);

        return response()->json(['data' => $review]);
    }

    public function setArchived(int $id): JsonResponse
    {
        $review = ReviewParsingLink::query()
            ->find($id)
            ->update(['status' => ReviewParsingLink::ARCHIVED]);

        return response()->json(['data' => $review]);
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
                ->mapWithKeys(fn(ReviewLinkPage $item): array => [$item['page_number'] => json_decode($item['body'] ,true)])
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

    public function parseByLinkIds(ReviewParsingRequest $request, ReviewParsingService $parserService): JsonResponse
    {
        set_time_limit(7200);
        $linkIds = $request->input('linkIds');
        $isLoadToDb = $request->boolean('isLoadToDb');



        $result = $parserService->parseLinks($linkIds, $isLoadToDb);

        return response()->json(['data' => $result, 'size' => MemoryUsage::getMemoryUsage()]);
    }
}
