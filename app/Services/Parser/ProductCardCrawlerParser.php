<?php

declare(strict_types=1);

namespace App\Services\Parser;


use App\Exceptions\ImageSavingException;
use App\Exceptions\TextExtractException;
use App\Exceptions\TextMethodException;
use App\Repositories\LinkRepository\ParsingLinkWithOptionsDTO;
use App\Services\ImageLoadingService\ImageLoadingInterface;
use App\Services\Parser\Contracts\AbstractProductCardParser;
use App\Services\Parser\DTO\ProductCardDTO;
use App\Services\UrlService\IUrlService;
use Symfony\Component\DomCrawler\Crawler;

class ProductCardCrawlerParser extends AbstractProductCardParser
{
    public const DESTINATION_FOLDER = 'public/image/sku/';
    protected Crawler $crawler;
    protected ImageLoadingInterface $imageLoadingService;
    protected IUrlService $urlService;


    public function __construct(ParsingLinkWithOptionsDTO $currentLink)
    {
        parent::__construct($currentLink);
        $this->crawler = new Crawler();
        $this->imageLoadingService = app(ImageLoadingInterface::class);
        $this->urlService = app(IUrlService::class);
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
                    throw new TextExtractException("Опция {$option[0]} {$e->getMessage()} {$e->getFile()} {$e->getLine()}");
                }

                $property = $option[0];
                if (
                    !is_null($current)
                    && isset($option[1])
                    && in_array($property, array_keys(get_class_vars(get_class($this->productCard))), true)
                ) {
                    $this->productCard->$property = $this->clearValues($current, $property, $option[1]);
                }
            }

            if (!isset($this->productCard->volume)) {
                $this->productCard->volume = $this->volume;
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
        $storeUrl = $urlArray['scheme'] . '://' . $urlArray['host'];


        $imgLinks = $this->crawler->filter($this->currentLink->imgTag);


        foreach ($imgLinks as $key => $imgLink) {
            $imgLink = $imgLink->getAttribute($this->currentLink->imgAttr);

            $this->productCard->imageLinks[] = $this->urlService->relativeUrlToAbsolute($imgLink, $storeUrl);

            if ($imgLink) {
                $fileName = $this->productCard->code . '-' . preg_replace('#\s+#', '', $this->productCard->volume) . '_' . ($key + 1);

                if (strpos($imgLink, 'http') === false && strpos($imgLink, 'https') === false) {
                    $imgLink = $storeUrl . $imgLink;
                }

                try {
                    $imageSavedPathResult = $this->imageLoadingService->loadingImage(self::DESTINATION_FOLDER, $imgLink, $fileName);

                    if (is_array($imageSavedPathResult->sizeOptions)) {
                        $this->productCard->images[] = $imageSavedPathResult->imageSavePath;
                    }
                } catch (ImageSavingException $e) {

                }
            }
        }
    }

    protected function extractValue(array $option): ?string
    {
        $current = '';

        if ($option[1] !== '' && !is_null($option[1])) {
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
