<?php

namespace App\Services\Parser;

use App\Exceptions\ImageSavingException;
use App\Models\ReviewParsingLink;
use App\Repositories\LinkRepository\ReviewLinkDTO;
use App\Services\ImageLoadingService\ImageLoadingService;
use App\Services\ImageLoadingService\ImageLoadingWithResizeService;
use App\Services\ProxyHttpClientService\ProxyHttpClientService;
use Symfony\Component\DomCrawler\Crawler;

class ReviewCrawlerParser
{
    const REVIEW = '.article-text-container';
    const REVIEW_TITLE = '.kf-post-title';
    const REVIEW_P = '.article-text-container p';

    const IMAGE_TAG = '.article-text-container figure picture img';

    const DESTINATION_FOLDER = 'public/image/review/';

    //protected ProductCardDTO $review;
    protected ReviewLinkDTO $currentLink;

    protected string $title = '';
    protected array $body = [];

    public function __construct(
        protected ProxyHttpClientService $httpClient,
        protected Crawler $crawler,
        protected ImageLoadingWithResizeService $imageLoadingService
    )
    {}

    public function setCurrentLink(ReviewLinkDTO $currentLink): self
    {
        $this->currentLink = $currentLink;
        return $this;
    }

    public function parse(bool $isLoadToDb = false): ?array
    {
        if ($body = $this->getContent()) {
            $this->crawler->addHtmlContent($body);

            $this->getTitle();
            $this->getBody();

            $result = ['title' => $this->title, 'body' => $this->body];

            if ($isLoadToDb) {
                ReviewParsingLink::query()
                    ->find($this->currentLink->id)
                    ->update(['content' => $result, 'status' => ReviewParsingLink::PARSED]);
            }

            return $result;
        }

        return null;
    }

    protected function getTitle(): void
    {
        $reviewTitle = $this->crawler->filter(self:: REVIEW_TITLE);
        if (1 === count($reviewTitle)) {
            $this->title = $reviewTitle->text();
        }
    }

    protected function getBody(): void
    {
        $body = $this->crawler
            ->filter(self:: REVIEW)
            ->children()
            ->children()
            ->children()
            ->each(function(Crawler $node, $i) {
                if ($node->nodeName() === 'figure') {
                    $node = $node->filter('img')->getNode(0);
                    if ($node) {

                        $imgLink = $node->getAttribute('src');

                        if ($imgLink) {
                            return $this->getImageBlock($this->processImages($imgLink));
                        }

                    }
                    return null;
                }

                return $this->getParagraphBlock($node->text());
            });

        $this->body = array_values(array_filter($body));

    }

    protected function getImageBlock(string $url): array
    {
        return ['data' => ['text' => $url], 'type' => 'image'];
    }

    protected function getParagraphBlock(string $text): ?array
    {
        if ('' === $text) {
            return null;
        }
        return ['data' => ['text' => $text], 'type' => 'paragraph'];
    }

    protected function processImages(string $imgLink): string
    {
        $urlParts = explode('/', $imgLink);
        $fileName = end($urlParts);

        try {
            $imageSavedPathResult = $this->imageLoadingService->loadingImage(self::DESTINATION_FOLDER, $imgLink, $fileName);

            if (is_array($imageSavedPathResult->sizeOptions)) {
                return $imageSavedPathResult->imageSavePath;
            }
        } catch (ImageSavingException $e) {

        }
    }

//    protected function getParagraphs(): void
//    {
//        $reviewParagraphs = $this->crawler->filter(self::REVIEW_P);
//
//        if (count($reviewParagraphs)) {
//            $this->paragraphs = $reviewParagraphs->each(function(Crawler $node, $i) {
//                return $node->text();
//            });
//
//            $this->paragraphs = array_values(array_filter($this->paragraphs));
//        }
//    }
//
//    protected function getImages(): void
//    {
//        $images = $this->crawler->filter(self::IMAGE_TAG);
//
//        if (count($images)) {
//            foreach($images as $image) {
//                $imgLink = $image->getAttribute('src');
//
//                if ($imgLink) {
//                    $urlParts = explode('/', $imgLink);
//                    $fileName = end($urlParts);
//
//                    try {
//                        $imageSavedPathResult = $this->imageLoadingService->loadingImage(self::DESTINATION_FOLDER, $imgLink, $fileName);
//
//                        if (is_array($imageSavedPathResult->sizeOptions)) {
//                            $this->imagesUrls[] = $imageSavedPathResult->imageSavePath;
//                        }
//                    } catch (ImageSavingException $e) {
//
//                    }
//                }
//            }
//        }
//    }


    protected function getContent(): ?string
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
                    ->update(['status' => ReviewParsingLink::ARCHIVED]);
            }
        }
        return $body;
    }
}
