<?php

namespace App\Repositories\LinkRepository;

use App\Models\LinkPage;
use App\Models\ParsingLink;
use Illuminate\Support\Facades\DB;
use stdClass;

class ProductLinkRepository implements LinkRepositoryInterface
{
    public function insertBody(int $linkOptionId, int $pageNumber, BodyDTO $body): void
    {
        LinkPage::query()->updateOrCreate(
            ['link_option_id' => $linkOptionId, 'page_number' => $pageNumber],
            ['body' => json_encode($body)]
        );
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

    public function getLinksWithOptionsByIds(array $ids): array
    {
        return DB::table('parsing_links', 'l')
            ->select(
                'l.id',
                'l.link',
                'l.store_id',
                'l.category_id',
                'l.body',
                'product_options.options',
                'stores.check_images_count'
            )
            ->join('product_options', 'l.store_id', '=','product_options.store_id')
            ->join('stores', 'stores.id', '=', 'l.store_id')
            ->whereIn('l.id', $ids)
            ->get()
            ->map(static function (stdClass $item): ParsingLinkWithOptionsDTO {
                $options = json_decode($item->options, true);

                $link = new ParsingLinkWithOptionsDTO();
                $link->id = (int)$item->id;
                $link->link = $item->link;
                $link->store_id = (int)$item->store_id;
                $link->category_id = (int)$item->category_id;
                $link->body = $item->body;
                $link->check_images_count = (bool)$item->check_images_count;
                $link->options = $options['fileFields'];
                $link->imgTag = $options['imgTag'];
                $link->imgAttr = $options['imgAttr'] ?? 'src';
                return $link;
            })
            ->all();
    }
}
