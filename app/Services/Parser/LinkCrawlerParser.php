<?php

declare(strict_types=1);

namespace App\Services\Parser;



use App\Services\Parser\Contracts\ILinkParser;
use App\Services\ProxyHttpClientService\ProxyHttpClientService;
use Symfony\Component\DomCrawler\Crawler;

class LinkCrawlerParser implements ILinkParser
{
    protected $crawler;

    public function __construct()
    {
        $this->crawler = new Crawler();
    }

    public function parseProductLinks(
        string $categoryPageUrl,
        string $storeUrl,
        string $productLink,
        bool $relatedProductLink,
        ?string $nextPage,
        bool $isRelatedPageUrl
    ): array
    {
        $categoryPageUrl = $isRelatedPageUrl ? $storeUrl . $categoryPageUrl : $categoryPageUrl;
        $result = [];
        $httpClient = app(ProxyHttpClientService::class);

        $response = $httpClient->request($categoryPageUrl);
        $code = $response->getStatusCode();



        if (in_array($code, [200, 301])) {
            $this->crawler->addHtmlContent($response->getBody()->getContents());

            $products = $this->crawler->filter($productLink);


            if (count($products)) {
                foreach ($products as $product) {
                    $link = $product->getAttribute('href');
                    $link = $relatedProductLink ? $storeUrl . $link : $link;

                    if ($link && !in_array($link, $result)) {
                        $result[] = $link;
                    }
                }
            }



            if ($nextPage) {
                $nextPageElements = $this->crawler->filter($nextPage);

                if (count($nextPageElements)) {
                    $nextPageElements = $nextPageElements->last();
                }

                $nextPageLink = $nextPageElements->getAttribute('href');


                if ($nextPageLink) {
                    $result = array_merge(
                        $result,
                        $this->parseProductLinks(
                            $nextPageLink,
                            $storeUrl,
                            $productLink,
                            $relatedProductLink,
                            $nextPage,
                            $isRelatedPageUrl
                        )
                    );
                }
            }
        }


        return $result;
    }
}
