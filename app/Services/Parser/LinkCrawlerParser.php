<?php

declare(strict_types=1);

namespace App\Services\Parser;

use App\Models\LinkPage;
use App\Services\Parser\Contracts\ILinkParser;
use App\Services\ProxyHttpClientService\ProxyHttpClientInterface;
use App\Services\UrlService\IUrlService;
use Symfony\Component\DomCrawler\Crawler;

class LinkCrawlerParser implements ILinkParser
{
    protected int $pageNumber = 0;
    protected int $linkOptionId;
    protected array $body;
    protected ?string $nextPage;
    protected string $productLink;
    protected string $storeUrl;

    public function __construct(
        protected ProxyHttpClientInterface $httpClient,
        protected IUrlService $urlService
    ){}

    public function setParsingOptions(
        int     $linkOptionId,
        array   $body,
        ?string $nextPage,
        string  $productLink,
        string  $storeUrl
    ): void {
        $this->linkOptionId = $linkOptionId;
        $this->body = $body;
        $this->nextPage = $nextPage;
        $this->productLink = $productLink;
        $this->storeUrl = $storeUrl;
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
                    $productLink = $product->getAttribute('href');


                    //$productLink = $this->isRelatedProductLink ? $this->storeUrl . $productLink : $productLink;

                    $productLink = $this->urlService->relativeUrlToAbsolute($productLink, $this->storeUrl);

                    if ($productLink && !in_array($productLink, $productLinks)) {
                        $productLinks['links'][] = $productLink;
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

            $productLinks['links'] = array_unique($productLinks['links']);
        }


        return $productLinks;
    }

    protected function getPageData(string $categoryPageUrl): array
    {
        if (!isset($this->body[$this->pageNumber])) {
//            if ($this->isRelatedPageUrl) {
//                $categoryPageUrl = $this->storeUrl . $categoryPageUrl;
//            }

            $categoryPageUrl = $this->urlService->relativeUrlToAbsolute($categoryPageUrl, $this->storeUrl);

            $response = $this->httpClient->request($categoryPageUrl);
            $code = $response->getStatusCode();
            $body = [
                'code' => $code,
                'content' => null
            ];

            if (in_array($code, [200, 301])) {
                $body['content'] = $response->getBody()->getContents();

                LinkPage::query()->updateOrCreate(
                    ['link_option_id' => $this->linkOptionId, 'page_number' => $this->pageNumber],
                    ['body' => json_encode($body)]);
            }

            $this->body[$this->pageNumber] = $body;
        }

        return $this->body[$this->pageNumber];
    }
}
