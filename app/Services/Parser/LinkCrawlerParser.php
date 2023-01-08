<?php

declare(strict_types=1);

namespace App\Services\Parser;



use App\Models\LinkOption;
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
        $result = [];

        if ($body = $this->getBody($categoryPageUrl)) {
            $crawler = new Crawler();
            $crawler->addHtmlContent($body);

            $products = $crawler->filter($this->productLink);


            if (count($products)) {
                foreach ($products as $product) {
                    $link = $product->getAttribute('href');
                    $link = $this->relatedProductLink ? $this->storeUrl . $link : $link;

                    if ($link && !in_array($link, $result)) {
                        $result[] = $link;
                    }
                }
            }



            if ($this->nextPage) {
                $nextPageElements = $crawler->filter($this->nextPage);

                if (count($nextPageElements)) {
                    $nextPageElements = $nextPageElements->last();
                }

                $nextPageLink = $nextPageElements->getAttribute('href');


                if ($nextPageLink) {
                    $result = array_merge($result, $this->parseProductLinks($nextPageLink));
                }
            }
        }

        return $result;
    }

    protected function getBody(string $categoryPageUrl): ?string
    {
        if (!$this->body[$this->pageNumber]) {
            $categoryPageUrl = $this->isRelatedPageUrl ? $this->storeUrl . $categoryPageUrl : $categoryPageUrl;

            $response = $this->httpClient->request($categoryPageUrl);
            $code = $response->getStatusCode();


            if (in_array($code, [200, 301])) {
                $this->body[$this->pageNumber] = $response->getBody()->getContents();
                LinkOption::where('id', $this->id)->update(['body' => json_encode($this->body)]);
            }
        }

        $this->pageNumber++;
        return $this->body[$this->pageNumber];
    }
}
