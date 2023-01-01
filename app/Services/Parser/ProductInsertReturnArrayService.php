<?php

declare(strict_types=1);

namespace App\Services\Parser;


use App\Exceptions\ProductInsertException;
use App\Models\Ingredient;
use App\Models\IngredientProduct;
use Exception;
use Illuminate\Support\Facades\DB;
use PDOException;

class ProductInsertReturnArrayService
{
    public function insertProductsInfo(array $parsedInfo, int $storeId, int $brandId): array
    {

        if (count($parsedInfo) > 0) {
            $timer1 = microtime(true);
            set_time_limit(7200);
            $brandPattern = '#[-+\s+]#';

            $preparedValues = $this->prepareParsedValues($parsedInfo);


            $productNameValues = $preparedValues['product'];
            $brandNameValues = $preparedValues['brand'];
            $countryNameValues = $preparedValues['country'];


            //return $brandNameValues;


            $countryIdsAndName = [];
            if (count($countryNameValues) > 0) {
                $countryIdsAndName = Utils::findExisting(
                    $this->country->findExisting($countryNameValues),
                    $countryNameValues,
                    '%'
                );
            }

            $brandIdsAndName = [];
            if (count($brandNameValues) > 0) {
                $brandIdsAndName = Utils::findExisting(
                    $this->brand->findExisting($brandNameValues),
                    $brandNameValues,
                    '%'
                );
            }

            $productIdsNamesAndBrandIds = [];
            $existingProducts = [];
            $skuIdVolumesAndProductIds = [];


            if (count($productNameValues) > 0 && count($brandIdsAndName) > 0) {
                $existingBrandIds = array_values($brandIdsAndName);
                $existingProducts = $this->product->findByNamesAndBrandIds($productNameValues, $existingBrandIds);

                $productIdsNamesAndBrandIds = Utils::findExistingProducts($existingProducts, $productNameValues, '%');

                if (count($existingProducts) > 0) {
                    $existingProductIds = array_column($existingProducts, 'id');
                    $existingSkus = $this->sku->findByProductIds($existingProductIds);

                    foreach ($existingSkus as $sku) {
                        $skuIdVolumesAndProductIds[$sku['product_id']][] = [
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

                    $brandName = preg_replace($brandPattern, '%', $insertRow['brand']);
                    if (array_key_exists($brandName, $brandIdsAndName)) {
                        $brandId = (int)$brandIdsAndName[$brandName];
                    } else {
                        $countryId = null;

                        if (isset($insertRow['country'])) {
                            $countryName = preg_replace($brandPattern, '%', $insertRow['country']);

                            if (array_key_exists($countryName, $countryIdsAndName)) {
                                $countryId = (int)$countryIdsAndName[$countryName];
                            } else {
                                $countryId = $this->country->insert(["name" => $insertRow['country']]);
                                $countryIdsAndName[$countryName] = $countryId;
                            }
                        }


                        $brandId = $this->brand->insert([
                            "name" => $insertRow['brand'],
                            "code" => Text::makeBrandCode($insertRow['brand']),
                            "country_id" => $countryId
                        ]);
                        $brandIdsAndName[$brandName] = $brandId;
                    }


                    //ищем,существует такой продукт или вставляем его в таблицу

                    [$isProductExist, $productId] = $this->isProductExist($productIdsNamesAndBrandIds, $brandId, $insertRow['name']);


                    if (!$isProductExist) {
                        $productId = $this->product->insert([
                            "name" => $insertRow['name'],
                            "name_en" => $insertRow['name_en'],
                            "description" => $insertRow['description'],
                            "brand_id" => $brandId,
                            "category_id" => $insertRow['category_id'],
                            "application" =>  $insertRow['application'] ??  NULL,
                            "purpose" => $insertRow['purpose'] ?? NULL,
                            "effect" => $insertRow['effect'] ?? NULL,
                            "age" =>  $insertRow['age'] ?? NULL,
                            "type_of_skin" => $insertRow['type_of_skin'] ?? NULL,
                            "code" => $insertRow['code'],
                        ]);




                        if (isset($insertRow['ingredient'])) {
                            $this->insertIngredients($insertRow['ingredient'], $productId);
                        }
                    }


                    if (!$this->isSkuExist($skuIdVolumesAndProductIds, $productId, $insertRow['volume'])) {

                        $skuId = $this->sku->insert([
                            "volume" => $insertRow['volume'],
                            "product_id" => $productId,
                            "images" => json_encode($insertRow['images'])
                        ]);

                        $this->priceHistory->insert([
                            "sku_id" => $skuId,
                            "store_id" => $storeId,
                            "link_id" => $insertRow['link_id'],
                            "price" =>  $insertRow['price']
                        ]);
                    }

                    $this->link->setLinkParsed($insertRow['link_id']);

                    DB::commit();

                } catch (PDOException $e) {
                    DB::rollback();
                    throw new ProductInsertException($e->getMessage());
                }
            }


            //$productsWithId[] = $db->fetch(PDO::FETCH_ASSOC);
            $duration = microtime(true) - $timer1;
            return [
                'duration' => $duration,
                'products' => $parsedInfo,
                'countryNameValues' => $countryNameValues,
                'countryIdsAndName' => $countryIdsAndName,
                'brandNameValues' => $brandNameValues,
                'brandIdsAndName' => $brandIdsAndName,
                'productNameValues' => $productNameValues,
                'existingProducts' => $existingProducts,
                'productIdsNamesAndBrandIds' => $productIdsNamesAndBrandIds,
                'lasIsProductExist' => $isProductExist,
                'skuIdVolumesAndProductIds' => $skuIdVolumesAndProductIds,
            ];
        }

        return  ['products' => 'не обнаружено'];
    }



    protected function isProductExist(array $productIdsNamesAndBrandIds,  int $brandId, string $insertedName): array
    {
        $insertedName = Utils::splitProductName($insertedName);

        if (array_key_exists($insertedName, $productIdsNamesAndBrandIds)) {
            foreach ($productIdsNamesAndBrandIds[$insertedName] as  $productName) {
                if ((int) $productName['brand_id'] === $brandId) {
                    return [true, (int) $productName['id']];
                }
            }
        }

        return [false, null];
    }



    protected function isSkuExist(array &$skuIdVolumesAndProductIds, int $productId, string $insertedVolume): bool
    {
        if (array_key_exists($productId, $skuIdVolumesAndProductIds)) {
            $insertingSkuVolume = preg_replace('#[^\d+]#', '', $insertedVolume);

            foreach ($skuIdVolumesAndProductIds[$productId] as $skuByProductId) {
                $existingSkuVolume = preg_replace('#[^\d+]#', '', $skuByProductId['volume']);

                if ($existingSkuVolume === $insertingSkuVolume) {
                    unset($skuIdVolumesAndProductIds[$productId]);
                    return true;
                }
            }
        }

        return false;
    }



    protected function prepareParsedValues(array $parsedInfo): array
    {
        $brandNameValues = [];
        $productNameValues = [];
        $countryNameValues = [];

        $pattern = '#[-+\s+]#';

        foreach ($parsedInfo as $product) {
            $brandValue = preg_replace($pattern, '%', $product['brand']);
            if (array_search($brandValue, $brandNameValues) === false) {
                $brandNameValues[] = $brandValue;
            }


            $productValue = Utils::splitProductName($product['name']);
            if (array_search($productValue, $productNameValues) === false) {
                $productNameValues[] = $productValue;
            }


            if (isset($product['country'])) {
                $countryValue = preg_replace($pattern, '%', $product['country']);
                if (array_search($countryValue, $countryNameValues) === false) {
                    $countryNameValues[] = $countryValue;
                }
            }
        }

        return [
            'brand' => $brandNameValues,
            'product' => $productNameValues,
            'country' => $countryNameValues,
        ];
    }




    protected function insertIngredients(array $ingredients, int $productId)
    {
        $ingredientOrder = 1;

        foreach ($ingredients as $ingredientName) {
            if ($ingredientName !== '') {
                $ingredientId = $this->insertIngredientByName($ingredientName);

                IngredientProduct::create([
                    "product_id" => $productId,
                    "ingredient_id" => $ingredientId,
                    "order" => $ingredientOrder
                ]);

                $ingredientOrder++;
            }
        }
    }

    public function insertIngredientByName(string $ingredientName): int
    {
        $code = Text::makeIngredientCode(trim($ingredientName));

        $currentIngredient = Ingredient::where(["code" => $code])->first();

        if (!$currentIngredient) {
            $lang = Utils::knownLanguage($ingredientName);
            try {
                if ($lang === 'en') {
                    $currentIngredient = Ingredient::create([
                        "name" => $ingredientName,
                        "code" => $code
                    ]);
                } elseif ($lang === 'rus') {
                    $currentIngredient = Ingredient::create([
                        "name_rus" => $ingredientName,
                        "code" => $code
                    ]);
                }
            } catch (\Throwable $e) {
                throw new Exception("Код ингредиента $code, имя $ingredientName " . $e->getMessage());
            }
        }

        return (int) $currentIngredient->id;
    }
}
