<?php

namespace App\Repositories\LinkRepository;

use App\Models\LinkPage;
use App\Models\ReviewLinkPage;
use App\Models\ReviewParsingLink;
use Illuminate\Support\Facades\DB;
use stdClass;

class ReviewLinkRepository implements LinkRepositoryInterface
{
    public function insertBody(int $linkOptionId, int $pageNumber, BodyDTO $body): void
    {
        ReviewLinkPage::query()->updateOrCreate(
            ['review_link_option_id' => $linkOptionId, 'page_number' => $pageNumber],
            ['body' => json_encode($body)]);
    }

    public function insert(array $parsedLinks, int $categoryId): void
    {
        $existingParsingLinks = ReviewParsingLink::query()
            ->select('link')
            ->whereIn('link', $parsedLinks)
            ->get()
            ->pluck('link')
            ->all();


        if (count($existingParsingLinks)) {
            $parsedLinks = array_filter(
                $parsedLinks,
                static function ($item) use ($existingParsingLinks) {
                    return !in_array($item, $existingParsingLinks, true);
                }
            );
        }

        $preparedParsedLinks = array_map(
            static function ($link) use ($categoryId) {
                return [
                    "link" => $link,
                    "status" => ReviewParsingLink::UNPARSED,
                    "category_id" => $categoryId
                ];
            },
            $parsedLinks
        );

        ReviewParsingLink::query()->upsert($preparedParsedLinks, []);
    }

    public function getLinksWithOptionsByIds(array $ids): array
    {
        return ReviewParsingLink::query()
            ->select('id', 'link', 'category_id', 'body')
            ->whereIn('id', $ids)
            ->get()
            ->map(static function (ReviewParsingLink $item): ReviewLinkDTO {
                return new ReviewLinkDTO(
                    $item->id,
                    $item->link,
                    $item->category_id,
                    $item->body
                );
            })
            ->all();
    }
}
