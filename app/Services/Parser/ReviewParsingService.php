<?php

namespace App\Services\Parser;

use App\Exceptions\ProductInsertException;
use App\Exceptions\TextExtractException;
use App\Repositories\LinkRepository\ReviewLinkRepository;
use Exception;

class ReviewParsingService
{
    public function __construct(private readonly ReviewLinkRepository $productLinkRepository){}
    public function parseLinks(array $linkIds, bool $isLoadToDb): array
    {
        $parsedInfo = [];
        $res = [];
        $res['message'] = 'success';


        foreach ($this->productLinkRepository->getLinksWithOptionsByIds($linkIds) as $currentLink) {
            try {
                /** @var ReviewCrawlerParser $parser */
                $parser = app(ReviewCrawlerParser::class);
                $parser->setCurrentLink($currentLink);
                $currentParsedItem = $parser->parse($isLoadToDb);

            } catch (TextExtractException $e) {
                return [
                    'message' => 'false',
                    'message5' => 'Ошибка при парсинге html '. $e->getMessage()
                ];
            }

            if (!is_null($currentParsedItem)) {
                $res['data'][] = $currentParsedItem;
            }
        }


        if (count($res['data']) === 0) {
            $res['message'] = 'нету спарсенной информации';
        }


        return $res;
    }
}
