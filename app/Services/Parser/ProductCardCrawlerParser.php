<?php

declare(strict_types=1);

namespace App\Services\Parser;


use App\Exceptions\TextExtractException;
use App\Exceptions\TextMethodException;
use App\Services\Parser\Contracts\AbstractProductCardParser;
use App\Services\Parser\DTO\ParsingLinkWithOptionsDTO;
use App\Services\Parser\DTO\ProductCardDTO;
use App\Services\ImageLoadingService\ImageLoadingService;
use Symfony\Component\DomCrawler\Crawler;

class ProductCardCrawlerParser extends AbstractProductCardParser
{
    const  DESTINATION_FOLDER = 'public/image/sku/';
    protected $crawler;
    protected $imageLoadingService;


    public function __construct(ParsingLinkWithOptionsDTO $currentLink)
    {
        parent::__construct($currentLink);
        $this->crawler = new Crawler();
        $this->imageLoadingService = new ImageLoadingService();
    }

    public function parseProductCard(): ?ProductCardDTO
    {
        if ($body = $this->getBody()) {
            $this->productCard->link = $this->currentLink->link;
            $this->productCard->images = [];
            $this->productCard->link_id = $this->currentLink->id;
            $this->productCard->category_id = $this->currentLink->category_id;

            $this->crawler->addHtmlContent($body);




            foreach ($this->currentLink->options as $option) {
                try {
                    $current = $this->extractValue($option);
                } catch (\Throwable $e) {
                    throw new TextExtractException('опция '. $option[0] .
                        ' '. $e->getMessage() .' '. $e->getFile() .' '.  $e->getLine());
                }

                $property = $option[0];
                if (
                    !is_null($current) &&
                    in_array($property, array_keys(get_class_vars(get_class($this->productCard))), true)
                ) {
                    $this->productCard->$property = $this->clearValues($current, $property, $option[1]);
                }
            }

            if (!isset($this->productCard->volume)) {
                $this->productCard->volume =  $this->volume;
            }

            $this->productCard->code = Text::makeProductCode($this->brandClearValue, $this->nameClearValue);

            $nameEn = Utils::makeEnglishProductName($this->nameClearValue, $this->brandClearValue);
            $this->productCard->name_en = $nameEn ?? null;

            $this->getImages();
            return $this->productCard;
        }

        return null;
    }

    protected function getImages(): void
    {
        $urlArray = parse_url($this->currentLink->link);
        $storeUrl =  $urlArray['scheme'] . '://' . $urlArray['host'];


        $imgLinks = $this->crawler->filter($this->currentLink->imgTag);


        foreach ($imgLinks as $key => $imgLink) {
            $imgLink = $imgLink->getAttribute($this->currentLink->imgAttr);
            $this->productCard->imageLinks[] = $imgLink;
            dd($this->productCard);
            if ($imgLink) {
                $fileName = $this->productCard->code . '-' . preg_replace('#\s+#', '', $this->productCard->volume) . '_' . ($key + 1);
                if (strpos($imgLink, 'http') === false && strpos($imgLink, 'https') === false) {
                    $imgLink = $storeUrl . $imgLink;
                }


                [$imgPath, $size] = $this->imageLoadingService->loadingImage(self::DESTINATION_FOLDER,  $imgLink, $fileName);

                if ($size !== 'no_exist') {
                    $this->productCard->images[] = $imgPath;
                } else {
                    $this->productCard->images[] =  $size;
                }
            }
        }
    }

    protected function extractValue(array $option): ?string
    {
        $current = '';

        if ($option[1] !== '') {
            $elements = $this->crawler->filter($option[1]);

            if (count($elements)) {
                if (isset($option[2]) && $option[2] !== '') {

                    $current = $elements->eq((int)$option[2]);

                } else {

                    if (isset($option[3]) && isset($option[4])) {

                        $valuesArray = $elements
                            ->each(function (Crawler $node, $i) use ($option) {
                                if (strpos($node->children($option[3])->text(), $option[4]) !== false) {
                                    if ($option[5] !== '') {
                                        return $node->children($option[5]);
                                    }
                                }
                            });

                        $current = array_values(array_filter($valuesArray));
                        if (!empty($current)) {
                            $current = $current[0];
                        } else {
                            return null;
                        }


                    } else {
                        $current = $elements->first();

                    }

                }
            } else {

                if (
                    isset($option[3]) && isset($option[4]) && isset($option[5])
                    && strpos($elements->filter($option[3])->text(), $option[4]) !== false
                ) {
                    if ($option[5] !== '') {
                        $current = $elements->filter($option[5]);
                    }
                } else {
                    $current = $elements;
                }

            }


            if (0 === $current->count()) {
                if ($option[0] === 'ingredient') {
                    $current = $current->html();
                } else {
                    $current = '';
                }

            } else {
                if ($option[0] === 'price') {
                    $current->children()->clear();  //удаляет вложенные элементы
                }


                try {
                    $current = $current->text();
                } catch (\Throwable $e) {
                    throw new TextMethodException('Ошибка в методе extractValue' .
                        ' ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine());
                }
            }
            return $current;
        }

        return $current;
    }
}
