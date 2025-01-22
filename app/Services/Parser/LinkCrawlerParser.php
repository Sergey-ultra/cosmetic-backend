<?php

declare(strict_types=1);

namespace App\Services\Parser;

use App\Exceptions\LinkNotFoundException;
use App\Repositories\LinkRepository\BodyDTO;
use App\Repositories\LinkRepository\LinkRepositoryInterface;
use App\Repositories\LinkRepository\ProductLinkRepository;
use App\Services\ProxyHttpClientService\ProxyHttpClientInterface;
use App\Services\UrlService\IUrlService;
use DOMNode;
use Symfony\Component\DomCrawler\Crawler;

class LinkCrawlerParser
{
    protected int  $pageNumber    = 0;
    protected ?int $endPageNumber = null;
    protected int  $linkOptionId;
    /** @var array{
     *     code: int,
     *     content: ?string
     * }
     */
    protected array                   $body;
    protected ?string                 $nextPage              = null;
    protected string                  $link;
    protected string                  $targetUrl;
    protected string                  $paginationQueryString = '';
    protected LinkRepositoryInterface $linkPageRepository;

    public function __construct(
        protected ProxyHttpClientInterface $httpClient,
        protected IUrlService $urlService
    ){
        $this->linkPageRepository = app(ProductLinkRepository::class);
    }

    public function setLinkOptionId(int $linkOptionId): self
    {
        $this->linkOptionId = $linkOptionId;
        return $this;
    }

    public function setStartPageNumber(int $pageNumber): self
    {
        $this->pageNumber = $pageNumber;
        return $this;
    }

    public function setEndPageNumber(?int $endPageNumber): self
    {
        $this->endPageNumber = $endPageNumber;
        return $this;
    }

    /** @param array{
     *     code: int,
     *     content: ?string
     * } $body
     */
    public function setBody(array $body): self
    {
        $this->body = $body;
        return $this;
    }

    public function setNextPage(?string $nextPage): self
    {
        $this->nextPage = $nextPage;
        return $this;
    }

    public function setLink(string  $link): self
    {
        $this->link = $link;
        return $this;
    }

    public function setTargetUrl(string $targetUrl): self
    {
        $this->targetUrl = $targetUrl;
        return $this;
    }

    public function setPaginationQueryString(string $paginationQueryString): self
    {
        $this->paginationQueryString = $paginationQueryString;
        return $this;
    }

    public function setLinkPageRepository(LinkRepositoryInterface $linkPageRepository): self
    {
        $this->linkPageRepository = $linkPageRepository;
        return $this;
    }

    /**
     * @return array
     */
    public function getParsedBodies(): array
    {
        return $this->body;
    }


    /**
     * @param string $categoryPageUrl
     * @return array
     */
    public function parseLinksFromPage(string $categoryPageUrl): array
    {
        $linkList = [];

        $pageData = $this->getPageData($categoryPageUrl);
        if ($body = $pageData['content']) {
            $this->pageNumber++;

            $crawler = new Crawler($body, null, $this->targetUrl);
//            $crawler->addHtmlContent();
            $parsingItems = $crawler->filter($this->link);

            if (count($parsingItems)) {
                /** @var DOMNode $parsingItem */
                foreach ($parsingItems as $parsingItem) {
                    $parsingLink = $parsingItem->getAttribute('href');
                    if (!$parsingLink) {
                        throw new LinkNotFoundException($parsingItem);
                    }
                    $parsingLink = $this->urlService->relativeUrlToAbsolute($parsingLink, $this->targetUrl);

                    if ($parsingLink && !in_array($parsingLink, $linkList)) {
                        $linkList[] = $parsingLink;
                    }
                }
            }

            if ($this->nextPage && (null === $this->endPageNumber || $this->pageNumber < $this->endPageNumber)) {
                $nextPageElements = $crawler->filter($this->nextPage);
                if (count($nextPageElements)) {
                    $nextPageElements = $nextPageElements->last();

                    $nextPageLink = $nextPageElements->link()->getUri();


                    if ($nextPageLink) {
                        $nextPageLinkList = $this->parseLinksFromPage($nextPageLink);
                        $linkList = array_merge($linkList, $nextPageLinkList);
                    }
                }
            }

            $linkList = array_unique($linkList);
        }


        return $linkList;
    }

    /**
     * @param string $categoryPageUrl
     * @return array
     */
    protected function getPageData(string $categoryPageUrl): array
    {
        if (!isset($this->body[$this->pageNumber])) {

            $categoryPageUrl = $this->urlService->relativeUrlToAbsolute($categoryPageUrl, $this->targetUrl);
            if ($this->pageNumber > 0) {
                $categoryPageUrl = sprintf('%s/%s%d', $categoryPageUrl, $this->paginationQueryString, $this->pageNumber + 1);
            }

            $response = $this->httpClient->request($categoryPageUrl);
            $code = $response->getStatusCode();

            $body = [
                'code' => $code,
                'content' => null,
            ];

            if (in_array($code, [200, 301])) {
                $body['content'] = $response->getBody()->getContents();

                $bodyDto = new BodyDTO($body['code'], $body['content']);
                $this->linkPageRepository->insertBody($this->linkOptionId, $this->pageNumber, $bodyDto);
            }

            $this->body[$this->pageNumber] = $body;
        }

        return $this->body[$this->pageNumber];
    }
}
