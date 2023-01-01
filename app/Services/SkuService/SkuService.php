<?php

declare(strict_types=1);


namespace App\Services\SkuService;



use App\Services\TransformImagePathService\TransformImagePathService;
use Illuminate\Pagination\LengthAwarePaginator;


class SkuService implements SkuInterface
{
    private array $allSkus = [];
    private string $smallImagesFolder;
    private array $resultWithOneSkus = [];
    private array $resultWithSkus = [];

    public function __construct(private TransformImagePathService $transformImagePathService)
    {}

    public function setAllSkus(array $allSkus): void
    {
        $this->allSkus = $allSkus;
    }

    public function setSmallImagesFolder(string $folder): void
    {
        $this->smallImagesFolder = $folder;
    }

    public function groupAllSkusWithPricesToOneSku(): self
    {
        $resultWithOneSkus = [];
        foreach ($this->allSkus as $price) {
            $priceIdKey = array_search($price['sku_id'], array_column($resultWithOneSkus, 'sku_id'));

            if ($priceIdKey === false) {
                $resultWithOneSkus[] = [
                    'id' => $price['id'],
                    'name' => $price['name'],
                    'code' => $price['code'],
                    'category_id' => $price['category_id'],
                    'category' => $price['category'],
                    'brand' => $price['brand'],
                    'sku_id' => $price['sku_id'],
                    'volume' => $price['volume'],
                    'images' => $price['images'],
                    'rating' => $price['rating'],
                    'reviews_count' => $price['reviews_count'],
                    'prices' => [
                        [
                            'price' => $price['price'],
                            'code' => $price['link_code'],
                            'name' => $price['store_name'],
                            'image' => $price['store_image'],
                        ]
                    ]
                ];
            } else {
                $resultWithOneSkus[$priceIdKey]['prices'][] = [
                    'price' => $price['price'],
                    'code' => $price['link_code'],
                    'name' => $price['store_name'],
                    'image' => $price['store_image'],
                ];
            }
        }

        $this->resultWithOneSkus = $resultWithOneSkus;
        return $this;
    }

    public function groupSkusToOneProduct(): self
    {
        $resultWithSkus = [];

        foreach ($this->resultWithOneSkus as $sku) {
            $productKey = array_search($sku['id'], array_column($resultWithSkus, 'id'));

            if ($productKey === false) {
                $resultWithSkus[] = [
                    'id' => $sku['id'],
                    'name' => $sku['name'],
                    'code' => $sku['code'],
                    'category_id' => $sku['category_id'],
                    'category' => $sku['category'],
                    'brand' => $sku['brand'],
                    'skus' => [
                        [
                            'id' => $sku['sku_id'],
                            'volume' => $sku['volume'],
                            'prices' => $sku['prices'],
                            'images' => $sku['images'],
                            'rating' => $sku['rating'],
                            'reviews_count' => $sku['reviews_count'],
                        ]
                    ]
                ];
            } else {
                $resultWithSkus[$productKey]['skus'][] = [
                    'id' => $sku['sku_id'],
                    'volume' => $sku['volume'],
                    'prices' => $sku['prices'],
                    'images' => $sku['images'],
                    'rating' => $sku['rating'],
                    'reviews_count' => $sku['reviews_count'],
                ];
            }
        }





        foreach($resultWithSkus as $key => &$product) {
            usort($product['skus'], function($a, $b) {
                if ($a['volume'] == $b['volume']) {
                    return 0;
                }
                return $a['volume'] > $b['volume'] ? 1 : -1;
            });


            $curr = $product['skus'][0];
            $images = json_decode($curr['images'], true);

            foreach($images as &$image) {
                $image = $this->transformImagePathService->getDestinationPath($image, $this->smallImagesFolder);
            }

            $product['currentSku'] = [
                'id' => $curr['id'],
                'prices' => $curr['prices'],
                'images' => $images,
                'rating' => number_format((float) $curr['rating'], 1, '.', ''),
                'reviews_count' => $curr['reviews_count'],
                'minPrice' => min(array_column($curr['prices'], 'price')),
                'maxPrice' => max(array_column($curr['prices'], 'price'))
            ];
            foreach ($product['skus'] as &$sku) {
                unset($sku['prices']);
                unset($sku['images']);
                unset($sku['rating']);
                unset($sku['reviews_count']);
            }
        }
        $this->resultWithSkus = $resultWithSkus;
        return $this;
    }




    public function sort(?string $sort): self
    {
        switch($sort) {
            case 'price-asc':
                usort($this->resultWithSkus, function($a, $b) {
                    if ($a['currentSku']['minPrice'] == $b['currentSku']['minPrice']) {
                        return 0;
                    }
                    return $a['currentSku']['minPrice'] < $b['currentSku']['minPrice'] ? -1 : 1;
                });
                break;
            case 'price-desc':
                usort($this->resultWithSkus, function($a, $b) {
                    if ($a['currentSku']['minPrice'] == $b['currentSku']['minPrice']) {
                        return 0;
                    }
                    return $a['currentSku']['minPrice'] > $b['currentSku']['minPrice'] ? -1 : 1;
                });
                break;
            case 'rating':
                usort($this->resultWithSkus, function($a, $b) {
                    if ($a['currentSku']['rating'] == $b['currentSku']['rating']) {
                        return 0;
                    }
                    return $a['currentSku']['rating'] > $b['currentSku']['rating'] ? -1 : 1;
                });

        }

        return $this;
    }

    public function paginate(int $page, int $perPage)
    {
        $offset = max(0, ($page - 1) * $perPage);
        $finalArray = array_slice($this->resultWithSkus, $offset, $perPage);

        $paginator = new LengthAwarePaginator($finalArray, count($this->resultWithSkus), $perPage, $page);
        $paginator->setPath(url()->current());
        $paginator->appends(['per_page' => $perPage]);
        return $paginator;
    }

}