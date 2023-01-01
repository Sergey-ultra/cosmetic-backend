<?php

declare(strict_types=1);


namespace App\Services\Parser;


use App\Services\Parser\Contracts\IPriceParser;
use App\Services\ProxyHttpClientService\ProxyHttpClientInterface;
use Symfony\Component\DomCrawler\Crawler;

class PriceCrawlerService implements IPriceParser
{

    public function __construct(protected ProxyHttpClientInterface $httpClient) {}

    public function parsePricesByLink(string $link, string $priceTag): array
    {
        $response = $this->httpClient->request($link);
        $code = $response->getStatusCode();

        $price = 0;

        if ($code === 200) {
            $crawler = new Crawler();
            $crawler->addHtmlContent($response->getBody()->getContents());
            $price = $crawler->filter($priceTag);

            if (count($price)) {
                $price = $price->last();
                $price->children()->clear();//удаляет вложенные элементы
            }

            $price = $price->text();

            $price = (int)preg_replace('#[^\d+]#', '', $price);
        }

        return [$code, $price];
    }
}
