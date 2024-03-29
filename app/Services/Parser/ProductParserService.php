<?php

declare(strict_types=1);

namespace App\Services\Parser;


use App\Exceptions\ProductInsertException;
use App\Exceptions\TextExtractException;
use App\Repositories\LinkRepository\ParsingLinkWithOptionsDTO;
use App\Repositories\LinkRepository\ProductLinkRepository;
use App\Services\Parser\DTO\ProductCardDTO;
use Exception;
use Illuminate\Support\Facades\DB;

class ProductParserService
{
    const MIN_PRICE = 100;

    public function __construct(
        private readonly ProductInsertService $productInsertService,
        private readonly ProductLinkRepository $productLinkRepository,
    ){}

    public function parseProducts(
        bool $isLoadToDb,
        array $linkIds,
        int $storeId,
        bool $isInsertIngredients = true,
        ?int $brandId = null
    ): array
    {
        $links = $this->productLinkRepository->getLinksWithOptionsByIds($linkIds);

        $parsedInfo = [];
        $abandonedProducts = [];
        $res = [];
        $res['message'] = 'success';


        foreach ($links as $currentLink) {
            try {
                $parser = new ProductCardCrawlerParser($currentLink);

                $currentParsedProduct = $parser->parseProductCard();

            } catch (TextExtractException $e) {
                return [
                    'message' => 'false',
                    'message5' => 'Ошибка при парсинге html '. $e->getMessage()
                ];
            }

            if (!is_null($currentParsedProduct)) {
                $res['data'][] = $currentParsedProduct;

                $imageCountCondition = !$currentLink->check_images_count || count($currentParsedProduct->images) > 0;

                if ($imageCountCondition && $currentParsedProduct->price > self::MIN_PRICE) {
                    $parsedInfo[] = $currentParsedProduct;
                } else {
                    $abandonedProducts[] = $currentParsedProduct;
                }
            }
        }

        if ($isLoadToDb) {
            $res['data'] = [];
            if (count($parsedInfo) === 0 && count($abandonedProducts) === 0) {
                $res['message'] = 'нету спарсенной информации';
            } else {
                $res['message'] = 'success';

                if (count($parsedInfo) > 0) {
                    try {
                        $res['data'][] = $this->productInsertService->insertProductsInfo($parsedInfo, $storeId, $isInsertIngredients, $brandId);
                    } catch (ProductInsertException $e) {
                        $res['message'] = 'Ошибка при вставки карточки товара в таблицы ' . $e->getMessage();
                    }
                }

                if (count($abandonedProducts) > 0) {
                    $res = array_merge($res, $this->insertAbandonedProductsToDb($abandonedProducts));
                }
            }
        }

        return $res;
    }


    protected function insertAbandonedProductsToDb(array $abandonedProducts): array
    {
        $linkIds = array_map(
            function (ProductCardDTO $product): int {
                return $product->link_id;
            },
            $abandonedProducts
        );

        try {
            DB::table('parsing_links')->whereIn('id', $linkIds)->update(['parsed' => 2]);

            $res['message2'] = 'Поле parsed ссылок ' . implode(', ', $linkIds) . 'поменяли статус parsed на 2';
        } catch (Exception $er) {
            $res['message4'] = 'Ошибка при вставки карточки товара в таблицы или обновлении статуса ссылок' . $er->getMessage();
        }

        return $res;
    }


//    protected function getLinksWithOptionsByIds(array $ids): array
//    {
//        $links = DB::table('parsing_links', 'l')
//            ->select(
//                'l.id',
//                'l.link',
//                'l.store_id',
//                'l.category_id',
//                'l.body',
//                'product_options.options',
//                'stores.check_images_count'
//            )
//            ->join('product_options', 'l.store_id', '=','product_options.store_id')
//            ->join('stores', 'stores.id', '=', 'l.store_id')
//            ->whereIn('l.id', $ids)
//            ->get()
//            ->toArray()
//        ;
//
//
//        return array_map(
//            static function($item): ParsingLinkWithOptionsDTO {
//                $options = json_decode($item->options, true);
//
//                $link = new ParsingLinkWithOptionsDTO();
//                $link->id = (int)$item->id;
//                $link->link =  $item->link;
//                $link->store_id = (int)$item->store_id;
//                $link->category_id = (int)$item->category_id;
//                $link->body = $item->body;
//                $link->check_images_count = (bool)$item->check_images_count;
//                $link->options = $options['fileFields'];
//                $link->imgTag = $options['imgTag'];
//                $link->imgAttr = $options['imgAttr'] ?? 'src';
//                return $link;
//            },
//            $links
//        );
//    }
}
