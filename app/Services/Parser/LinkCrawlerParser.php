<?php

declare(strict_types=1);

namespace App\Services\Parser;



use App\Models\LinkOption;
use App\Models\LinkPage;
use App\Services\Parser\Contracts\ILinkParser;
use App\Services\ProxyHttpClientService\ProxyHttpClientInterface;
use App\Services\ProxyHttpClientService\ProxyHttpClientService;
use Symfony\Component\DomCrawler\Crawler;

class LinkCrawlerParser implements ILinkParser
{
    protected ProxyHttpClientInterface $httpClient;
    protected int $pageNumber = 0;

    public function __construct(
        protected int $id,
        protected array $body,
        protected ?string $nextPage,
        protected bool $relatedProductLink,
        protected string $productLink,
        protected bool $isRelatedPageUrl,
        protected string $storeUrl
    ) {
        $this->httpClient = app(ProxyHttpClientService::class);
    }



    public function parseProductLinks(string $categoryPageUrl): array
    {
        $pageData = $this->getPageData($categoryPageUrl);

        $body = $pageData['content'];
        $productLinks = [
            'codes' => [$pageData['code']],
            'links' => []
        ];

        if ($body) {
            $this->pageNumber++;
            $crawler = new Crawler();
            $crawler->addHtmlContent($body);

            $products = $crawler->filter($this->productLink);


            if (count($products)) {
                foreach ($products as $product) {
                    $link = $product->getAttribute('href');
                    $link = $this->relatedProductLink ? $this->storeUrl . $link : $link;

                    if ($link && !in_array($link, $productLinks)) {
                        $productLinks['links'][] = $link;
                    }
                }
            }



            if ($this->nextPage) {
                $nextPageElements = $crawler->filter($this->nextPage);

                if (count($nextPageElements)) {
                    $nextPageElements = $nextPageElements->last();
                }


                $nextPageLink = $nextPageElements->link()->getUri();


                if ($nextPageLink) {
                    $parsingProductLinks = $this->parseProductLinks($nextPageLink);
                    $productLinks['links'] = array_merge($productLinks['links'], $parsingProductLinks['links']);
                    $productLinks['codes'] = array_merge($productLinks['codes'], $parsingProductLinks['codes']);
                }
            }
        }


        return $productLinks;
    }

    protected function getPageData(string $categoryPageUrl): array
    {
        if (!isset($this->body[$this->pageNumber])) {
            if ($this->isRelatedPageUrl) {
                $categoryPageUrl = $this->storeUrl . $categoryPageUrl;
            }

            $response = $this->httpClient->request($categoryPageUrl);
            $code = $response->getStatusCode();
            $body = [
                'code' => $code,
                'content' => null
            ];

            if (in_array($code, [200, 301])) {
                $body['content'] = $response->getBody()->getContents();

                LinkPage::query()->create([
                    'link_option_id' => $this->id,
                    'page_number' => $this->pageNumber,
                    'body' => $body
                ]);
            }

            $this->body[$this->pageNumber] = $body;
        }

        return $this->body[$this->pageNumber];
    }
}
