<?php

namespace App\Repositories\LinkRepository;

use App\Models\LinkPage;
use App\Models\ParsingLink;

class ProductLinkRepository implements LinkRepositoryInterface
{
    public function insertBody(int $linkOptionId, int $pageNumber, BodyDTO $body): void
    {
        LinkPage::query()->updateOrCreate(
            ['link_option_id' => $linkOptionId, 'page_number' => $pageNumber],
            ['body' => json_encode($body)]);
    }

    /**
     * @param array $parsedLinks
     * @param int $storeId
     * @param int $categoryId
     * @return void
     */
    public function insert(array $parsedLinks, int $storeId, int $categoryId): void
    {
        $existingParsingLinks = ParsingLink::query()
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
            static function ($link) use ($storeId, $categoryId) {
                return [
                    "link" => $link,
                    "parsed" => ParsingLink::UNPARSED,
                    "store_id" => $storeId,
                    "category_id" => $categoryId
                ];
            },
            $parsedLinks
        );

        ParsingLink::query()->upsert($preparedParsedLinks, []);
    }
}
