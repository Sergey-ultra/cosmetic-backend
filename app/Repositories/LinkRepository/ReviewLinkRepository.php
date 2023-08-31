<?php

namespace App\Repositories\LinkRepository;

use App\Models\LinkPage;
use App\Models\ReviewLinkPage;
use App\Models\ReviewParsingLinks;

class ReviewLinkRepository implements  LinkRepositoryInterface
{
    public function insertBody(int $linkOptionId, int $pageNumber, BodyDTO $body): void
    {
        ReviewLinkPage::query()->updateOrCreate(
            ['review_link_option_id' => $linkOptionId, 'page_number' => $pageNumber],
            ['body' => json_encode($body)]);
    }

    public function insert(array $parsedLinks, int $categoryId): void
    {
        $existingParsingLinks = ReviewParsingLinks::query()
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
                    "parsed" => ReviewParsingLinks::UNPARSED,
                    "category_id" => $categoryId
                ];
            },
            $parsedLinks
        );

        ReviewParsingLinks::query()->upsert($preparedParsedLinks, []);
    }
}
