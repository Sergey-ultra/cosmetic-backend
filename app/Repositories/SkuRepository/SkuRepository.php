<?php

declare(strict_types=1);

namespace App\Repositories\SkuRepository;

use App\Models\Product;
use App\Models\Review;
use App\Models\ReviewView;
use App\Models\Sku;
use App\Repositories\SkuRepository\DTO\SkuDTO;
use App\Repositories\SkuRepository\DTO\SkuListOptionDTO;
use App\Services\EntityStatus;
use App\Services\Parser\Text;
use App\Services\Parser\Utils;
use App\Services\TransformImagePathService\TransformImagePathService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;


class SkuRepository implements ISkuRepository
{
    public const DEFAULT_PER_PAGE = 24;
    private array $allSkus = [];
    private string $smallImagesFolder;
    private array $resultWithOneSkus = [];
    private array $resultWithSkus = [];
    private ?int $entityId;
    private string $mode;

    private SkuListOptionDTO $skuListOptionDto;

    public function __construct(private TransformImagePathService $transformImagePathService)
    {}

    public function setMode(string $mode, ?int $entityId): self
    {
        $this->mode = $mode;
        $this->entityId = $entityId;
        return $this;
    }

    public function getList(SkuListOptionDTO $skuListOptionDto): LengthAwarePaginator
    {
        $this->skuListOptionDto = $skuListOptionDto;
        return $this->getAllSkus()
            ->groupAllSkusWithPricesToOneSku()
            ->groupSkusToOneProduct()
            ->sort()
            ->paginate();
    }

    private function getAllSkus(): self
    {
        $brandIds = $this->skuListOptionDto->brandIds;
        $categoryIds = $this->skuListOptionDto->categoryIds;
        $activeIngredientsGroupIds = $this->skuListOptionDto->activeIngredientsGroupIds;
        $countryIds = $this->skuListOptionDto->countryIds;
        $volumes = $this->skuListOptionDto->volumes;
        $maxPrice = $this->skuListOptionDto->maxPrice;
        $minPrice = $this->skuListOptionDto->minPrice;
        $search = $this->skuListOptionDto->search;



        //$productQuery = Product::with(['skus', 'brand'])->where('category_id' , $category->id);
        //$products = ProductsWithBrandAndSkuResource::collection($productData)->response()->getData();
        //$productData = $productData->paginate($perPage);
        //$products = ProductsWithBrandAndSkuResource::collection($productData)->response()->getData();

        $productQuery = Product::query()->select(
            'products.id as id',
            'products.name as name',
            'products.code as code',
            'products.category_id as category_id',
            'categories.name as category',
            'brands.name as brand',
            'skus.id as sku_id',
            'skus.volume as volume',
            'skus.images as images',
            'skus.rating as rating',
            'sku_store.price as price',
            'links.code as link_code',
            'stores.name as store_name',
            'stores.image as store_image',
            'skus.reviews_count as reviews_count'
        )
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('skus', 'products.id', '=', 'skus.product_id')
            ->join('sku_store', 'skus.id', '=', 'sku_store.sku_id')
            ->join('stores', 'sku_store.store_id', '=', 'stores.id')
            ->join('links', 'sku_store.link_id', '=', 'links.id')
            ->join('brands', 'products.brand_id', '=', 'brands.id');


        if ($activeIngredientsGroupIds) {
            $activeIngredientsQuery = DB::table('active_ingredients_group_ingredient')
                ->select('ingredient_product.product_id AS product_id')
                ->join('ingredient_product', 'ingredient_product.ingredient_id', '=', 'active_ingredients_group_ingredient.ingredient_id')
                ->whereIn('active_ingredients_group_ingredient.active_ingredients_group_id', $activeIngredientsGroupIds)
                ->groupBy('ingredient_product.product_id')
            ;


            $productQuery
                ->joinSub($activeIngredientsQuery,'ingredients', function($join) {
                    $join->on('products.id', '=', 'ingredients.product_id');
                });
        }

        $productQuery->whereNotNull('sku_store.price');


        if ($this->mode === 'category') {
            $productQuery->where('products.category_id', $this->entityId);
        } else if ($this->mode === 'brand') {
            $productQuery->where('products.brand_id', $this->entityId);
        } else if ($this->mode === 'search' && $search) {
            $productQuery->where('products.name', 'LIKE', "%$search%");
//            $productQuery = $productQuery->whereRaw(
//                "MATCH(products.name, products.name_en, products.description, products.application) AGAINST(?)",
//                [$search]
//            );
        }


        if ($brandIds) {
            $productQuery->whereIn('products.brand_id', $brandIds);
        }
        if ($countryIds) {
            $productQuery->whereIn('brands.country_id', $countryIds);
        }
        if ($categoryIds) {
            $productQuery->whereIn('products.category_id', $categoryIds);
        }
        if ($volumes) {
            $productQuery->whereIn('skus.volume', $volumes);
        }
        if ($minPrice) {
            $productQuery->where('sku_store.price', '>', $minPrice);
        }
        if ($maxPrice) {
            $productQuery->where('sku_store.price', '<', $maxPrice);
        }

        $this->allSkus = $productQuery->get()->toArray();

        return $this;
    }

    public function setSmallImagesFolder(string $folder): void
    {
        $this->smallImagesFolder = $folder;
    }

    private function groupAllSkusWithPricesToOneSku(): self
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

    private function groupSkusToOneProduct(): self
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

    private function sort(): self
    {
        switch($this->skuListOptionDto->sort) {
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

    private function paginate(): LengthAwarePaginator
    {
        $perPage = $this->skuListOptionDto->perPage ?? self::DEFAULT_PER_PAGE;
        $offset = max(0, ($this->skuListOptionDto->page - 1) * $perPage);

        $finalArray = array_slice($this->resultWithSkus, $offset, $perPage);

        $paginator = new LengthAwarePaginator($finalArray, count($this->resultWithSkus), $perPage, $this->skuListOptionDto->page);
        $paginator->setPath(url()->current());
        $paginator->appends(['per_page' => $perPage]);
        return $paginator;
    }

    /**
     * @return array
     */
    public function popularTenSkus(): array
    {
        return Product::query()
            ->select(
                sprintf('%s.name', Product::TABLE),
                sprintf('%s.volume', Sku::TABLE),
                DB::raw(sprintf('COUNT(%s.sku_id) AS views_count', Review::TABLE))
            )
            ->join(
                Sku::TABLE,
                sprintf('%s.product_id', Sku::TABLE),
                '=',
                sprintf('%s.id', Product::TABLE)
            )
            ->join(
                Review::TABLE,
                sprintf('%s.sku_id', Review::TABLE),
                '=',
                sprintf('%s.id', Sku::TABLE)
            )
            ->join(
                ReviewView::TABLE,
                sprintf('%s.review_id', ReviewView::TABLE),
                '=',
                sprintf('%s.id', Review::TABLE)
            )
            ->groupBy(
                sprintf('%s.sku_id', Review::TABLE),
                sprintf('%s.name', Product::TABLE),
                sprintf('%s.volume', Sku::TABLE),
            )
            ->orderBy('views_count', 'DESC')
            ->limit(10)
            ->get()
            ->toArray();
    }

    /**
     * @param SkuDTO $sku
     * @return array
     * @throws Throwable
     */
    public function createNewSku(SkuDTO $sku): array
    {
        $userId = Auth::guard('api')->user()->id;
        try {
            DB::beginTransaction();
            $newProduct = Product::query()->create([
                'category_id' => $sku->categoryId,
                'brand_id' => $sku->brandId,
                'name' => $sku->name,
                'name_en' => Utils::makeEnglishProductName($sku->name, $sku->brandName),
                'description' => $sku->description,
                'code' => Text::makeProductCode($sku->brandName, $sku->name),
                'user_id' => $userId,
            ]);

            $newSku = $newProduct->skus()->create([
                "volume" => $sku->volume,
                "product_id" => $newProduct->id,
                'rating' => 5,
                "images" => $sku->images,
                'status' => $sku->status,
                'user_id' => $userId,
            ]);

            DB::commit();
            return [
                'sku_id' => $newSku->id,
                'name' => $newProduct->name,
                'sku_code' => sprintf('%s-%s', $newProduct->code, $newSku->id),
                'volume' => $newSku->volume,
                'rating' => $newSku->rating,
                'reviews_count' => 0,
                'question_count' => 0,
                'images' => $newSku->images
            ];
        } catch (Throwable $e) {
            DB::rollback();
            throw $e;
        }
    }

}
