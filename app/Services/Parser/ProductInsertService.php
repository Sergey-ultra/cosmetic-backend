<?php
declare(strict_types=1);

namespace App\Services\Parser;


use App\Exceptions\ProductInsertException;
use App\Models\Brand;
use App\Models\Country;
use App\Models\Link;
use App\Models\ParsingLink;
use App\Models\PriceHistory;
use App\Models\Product;
use App\Models\Sku;
use Illuminate\Support\Facades\DB;

class ProductInsertService extends ProductInsertReturnArrayService
{

    public function insertProductsInfo(array $parsedInfo, int $storeId, bool $isInsertIngredients = true, ?int $brandId = null): array
    {
        if (count($parsedInfo) > 0) {
            $timer1 = microtime(true);
            set_time_limit(7200);
            $brandPattern = '#[-+\s+]#';


            if (!isset($brandId)) {
                [$brandIdsAndName, $countryIdsAndName] = $this->prepareExistingBrandsAndCountries($parsedInfo);
            }



            $existingProducts = [];



            $productNameValues = $this->preparedProductNameValues($parsedInfo);

            $brandCondition = isset($brandId) ? true : count($brandIdsAndName) > 0;

            if (count($productNameValues) > 0 && $brandCondition) {
                $existingProducts = Product::with('ingredientIds')
                    ->select('id', 'name', 'brand_id')
                    ->where(function ($query) use ($productNameValues) {
                        foreach ($productNameValues as $name) {
                            $query->orWhere('name', 'like', $name);
                        }
                    });

                if (!$brandId) {
                    $existingProducts->whereIn('brand_id', array_values($brandIdsAndName));
                } else {
                    $existingProducts->where('brand_id', $brandId);
                }

                $existingProducts = $existingProducts->get()->toArray();
                $this->productIdsNamesAndBrandIds = Utils::findExistingProducts($existingProducts, $productNameValues, '%');

                if (count($existingProducts) > 0) {
                    $existingProductIds = array_column($existingProducts, 'id');


                    $existingSkus = Sku::query()->select('id', 'volume', 'product_id')
                        ->whereIn('product_id', $existingProductIds)
                        ->get();

                    foreach ($existingSkus as $sku) {
                        $this->skuIdVolumesAndProductIds[$sku['product_id']][] = [
                            'id' => $sku['id'],
                            'volume' => $sku['volume']
                        ];
                    }
                }
            }



            //return $brandIdsAndName;
            foreach ($parsedInfo as $insertRow) {
                try {
                    DB::beginTransaction();

                    if (!isset($brandId)) {
                        $brandName = preg_replace($brandPattern, '%', $insertRow->brand);
                        if (array_key_exists($brandName, $brandIdsAndName)) {
                            $brandId = (int) $brandIdsAndName[$brandName];
                        } else {
                            $countryId = null;

                            if (isset($insertRow->country)) {
                                $countryName = preg_replace($brandPattern, '%', $insertRow->country);

                                if (array_key_exists($countryName, $countryIdsAndName)) {
                                    $countryId = (int)$countryIdsAndName[$countryName];
                                } else {
                                    $insertedCountry = Country::query()->create(["name" => $insertRow->country]);
                                    $countryId = (int)$insertedCountry->id;
                                    $countryIdsAndName[$countryName] = $countryId;
                                }
                            }

                            $insertedBrand = Brand::query()->create([
                                "name" => $insertRow->brand,
                                "code" => Text::makeCode($insertRow->brand),
                                "country_id" => $countryId
                            ]);
                            $brandId = (int)$insertedBrand->id;
                            $brandIdsAndName[$brandName] = $brandId;
                        }
                    }


                    //ищем,существует такой продукт или вставляем его в таблицу

                    [$isProductExist, $productId, $ingredientsCount] = $this->isProductExist($brandId, $insertRow->name);


                    if (!$isProductExist) {

                        $insertedProduct = Product::query()->create([
                            "brand_id" => $brandId,
                            "category_id" => $insertRow->category_id,
                            "name" => $insertRow->name,
                            "name_en" => $insertRow->name_en,
                            "description" => $insertRow->description,
                            "code" => $insertRow->code,
                            "application" =>  $insertRow->application ??  NULL,
                            "purpose" => $insertRow->purpose ?? NULL,
                            "effect" => $insertRow->effect ?? NULL,
                            "age" =>  $insertRow->age ?? NULL,
                            "type_of_skin" => $insertRow->type_of_skin ?? NULL
                        ]);


                        $productId = (int) $insertedProduct->id;
                    }

                    if ($isInsertIngredients && $ingredientsCount === 0 && isset($insertRow->ingredient)) {
                        $this->insertIngredients($insertRow->ingredient, $productId);
                    }


                    [$isSkuExist, $skuId] = $this->isSkuExist($productId, $insertRow->volume);


                    if (!$isSkuExist) {
                        $insertedSku = Sku::query()->create([
                            "volume" => $insertRow->volume,
                            "product_id" => $productId,
                            'rating' => 5,
                            //"images" => json_encode($insertRow->images),
                            "images" => $insertRow->images
                        ]);

                        $skuId = $insertedSku->id;
                    }
// пока не нужно писать в таблицу актуальных цен

//                    SkuStore::query()->updateOrCreate(
//                        ['sku_id' => $skuId, 'store_id' => $storeId, 'link_id' => $insertRow->link_id],
//                        [
//                            'price' => $insertRow->price,
//                            'fails_count' => DB::raw('IF(price IS NULL, fails_count + 1, fails_count)')
//                        ]
//                    );

                    PriceHistory::query()->create([
                        "sku_id" => $skuId,
                        "store_id" => $storeId,
                        "link_id" => $insertRow->link_id,
                        "price" => $insertRow->price
                    ]);

                    ParsingLink::query()->where('id', $insertRow->link_id)->update(['parsed'=> 1]);

                    Link::query()->create([
                        'id' => $insertRow->link_id,
                        'link' => $insertRow->link,
                        'code' => Token::getToken(),
                        'store_id'  => $storeId,
                        'category_id' => $insertRow->category_id
                    ]);

                    DB::commit();

                } catch (\Throwable $e) {
                    DB::rollback();
                    throw new ProductInsertException($e->getMessage() . ' '. $e->getFile() . ' ' . $e->getLine());
                }
            }

            $duration = microtime(true) - $timer1;
            return [
                'duration' => $duration,
                'products' => $parsedInfo,
                //'countryNameValues' => $countryNameValues,
                //'brandNameValues' => $brandNameValues,
                'countryIdsAndName' => isset($countryIdsAndName) ?? [],
                'brandIdsAndName' => isset($brandIdsAndName) ?? [],
                'productNameValues' => $productNameValues,
                'existingProducts' => $existingProducts,
                'productIdsNamesAndBrandIds' => $this->productIdsNamesAndBrandIds,
                'isProductExist' => $isProductExist,
                'skuIdVolumesAndProductIds' => $this->skuIdVolumesAndProductIds,
            ];
        }

        return  ['products' => 'не обнаружено'];
    }




    protected function prepareExistingBrandsAndCountries(array $parsedInfo): array
    {
        $brandNameValues = [];
        $countryNameValues = [];

        $pattern = '#[-+\s+]#';

        foreach ($parsedInfo as $product) {
            if (isset($product->country)) {
                $countryValue = preg_replace($pattern, '%', $product->country);
                if (array_search($countryValue, $countryNameValues) === false) {
                    $countryNameValues[] = $countryValue;
                }
            }

            $brandValue = preg_replace($pattern, '%', $product->brand);
            if (array_search($brandValue, $brandNameValues) === false) {
                $brandNameValues[] = $brandValue;
            }
        }




        $countryIdsAndName = [];
        if (count($countryNameValues) > 0) {
            $existingCountries = Country::query()
                ->select('id', 'name')
                ->where(function ($query) use ($countryNameValues) {
                    foreach ($countryNameValues as $name) {
                        $query->orWhere('name', 'like', $name);
                    }
                })
                ->get()
                ->toArray()
            ;

            $countryIdsAndName = Utils::findExisting($existingCountries, $countryNameValues, '%');
        }

        $brandIdsAndName = [];
        if (count($brandNameValues) > 0) {
            $existingBrands = Brand::select('id', 'name')
                ->where(function ($query) use ($brandNameValues) {
                    foreach ($brandNameValues as $name) {
                        $query->orWhere('name', 'like', $name);
                    }
                })
                ->get()
            ->toArray();

            $brandIdsAndName = Utils::findExisting($existingBrands, $brandNameValues, '%');
        }

        return [$brandIdsAndName, $countryIdsAndName];
    }



    protected function preparedProductNameValues(array $parsedInfo): array
    {
        $productNameValues = [];
        foreach ($parsedInfo as $product) {
            $productValue = Utils::splitProductName($product->name);
            if (array_search($productValue, $productNameValues) === false) {
                $productNameValues[] = $productValue;
            }
        }
        return $productNameValues;
    }
}
