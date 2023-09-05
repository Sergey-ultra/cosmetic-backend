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
    protected array $paragraphs = [];

    protected array $imagesUrls = [];
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
        if ($body = $this->getBody()) {
            $this->crawler->addHtmlContent($body);

            $this->getTitle();
            $this->getParagraphs();
            $this->getImages();


            $result = ['title' => $this->title, 'images' => $this->imagesUrls, 'paragraphs' => $this->paragraphs];

            if ($isLoadToDb) {
                ReviewParsingLink::query()
                    ->find($this->currentLink->id)
                    ->update(['content' => $result, 'parsed' => ReviewParsingLink::PARSED]);
            }

            //$this->getImages();
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

    protected function getParagraphs(): void
    {
        $reviewParagraphs = $this->crawler->filter(self::REVIEW_P);

        if (count($reviewParagraphs)) {
            $this->paragraphs = $reviewParagraphs->each(function(Crawler $node, $i) {
                return $node->text();
            });

            $this->paragraphs = array_values(array_filter($this->paragraphs));
        }
    }

    protected function getImages(): void
    {
        $images = $this->crawler->filter(self::IMAGE_TAG);

        if (count($images)) {
            foreach($images as $image) {
                $imgLink = $image->getAttribute('src');

                if ($imgLink) {
                    $urlParts = explode('/', $imgLink);
                    $fileName = end($urlParts);

                    try {
                        $imageSavedPathResult = $this->imageLoadingService->loadingImage(self::DESTINATION_FOLDER, $imgLink, $fileName);

                        if (is_array($imageSavedPathResult->sizeOptions)) {
                            $this->imagesUrls[] = $imageSavedPathResult->imageSavePath;
                        }
                    } catch (ImageSavingException $e) {

                    }
                }
            }
        }
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
