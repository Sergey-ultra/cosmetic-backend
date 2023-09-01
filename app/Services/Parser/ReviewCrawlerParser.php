<?php

namespace App\Services\Parser;

use App\Models\ReviewParsingLink;
use App\Repositories\LinkRepository\ReviewLinkDTO;
use App\Services\ProxyHttpClientService\ProxyHttpClientService;
use Symfony\Component\DomCrawler\Crawler;

class ReviewCrawlerParser
{
    const REVIEW = '.article-text-container';
    const REVIEW_P = '.article-text-container p';

    const IMAGE_TAG = '.article-text-container figure picture img';

    //protected ProductCardDTO $review;
    protected ReviewLinkDTO $currentLink;
    protected Crawler $crawler;
    public function __construct(protected ProxyHttpClientService $httpClient)
    {
        //$this->review = new ProductCardDTO();
        $this->crawler = new Crawler();
    }

    public function setCurrentLink(ReviewLinkDTO $currentLink): self
    {
        $this->currentLink = $currentLink;
        return $this;
    }

    public function parse(bool $isLoadToDb = false): ?array
    {
        if ($body = $this->getBody()) {
            $this->crawler->addHtmlContent($body);

            $reviewParagraphs = $this->crawler->filter(self::REVIEW_P);


            $paragraphs = [];
            if (count($reviewParagraphs)) {
                $paragraphs = $reviewParagraphs->each(function(Crawler $node, $i) {
                    return $node->text();
                });

                $paragraphs = array_values(array_filter($paragraphs));
            }


            $images = $this->crawler->filter(self::IMAGE_TAG);

            $imagesUrls = [];
            if (count($images)) {
                foreach($images as $image) {
                    $imagesUrls[] = $image->getAttribute('src');
                }
            }

            $result = ['images' => $imagesUrls, 'paragraphs' => $paragraphs];
            if ($isLoadToDb) {
                ReviewParsingLink::query()
                    ->find($this->currentLink->id)
                    ->update(['json' => $result, 'parsed' => ReviewParsingLink::PARSED]);
            }

            //$this->getImages();
            return $result;
        }

        return null;
    }


    protected function getBody(): ?string
    {
        $body = $this->currentLink->body;
        if (!$body) {
            $response = $this->httpClient->request($this->currentLink->link);
            $code = $response->getStatusCode();

            if ($code === 200) {
                $body = $response->getBody()->getContents();
                ReviewParsingLink::query()
                    ->find($this->currentLink->id)
                    ->update(['body' => $body]);
            } else if ($code === 404) {
                ReviewParsingLink::query()
                    ->find($this->currentLink->id)
                    ->update(['parsed' => ReviewParsingLink::ABANDONED]);
            }
        }
        return $body;
    }
}
