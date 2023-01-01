<?php

namespace App\Http\Controllers\Api\Admin\Parser\Old;


use App\Http\Controllers\Controller;
use App\Jobs\ImageLoadingJob;
use App\Jobs\PriceDropJob;
use App\Models\Category;
use App\Models\Country;
use App\Models\Ingredient;
use App\Models\Link;
use App\Models\PriceHistory;
use App\Models\Product;
use App\Models\Sku;
use App\Models\SkuStore;
use App\Models\Store;
use App\Models\Tracking;
use App\Services\ParserService;
use Exception;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ParserController extends Controller
{
    protected $parserService;

    public function __construct(ParserService $parserService)
    {
        $this->parserService = $parserService;
    }

    public function saveBrand()
    {
        $rawData = $this->parserService->getBrands();

        $message = 'Нет данных в апи парсера';

        if (count($rawData) > 0) {
            try {
                DB::beginTransaction();
                Brand::upsert(
                    $rawData,
                    []);
                DB::commit();

            } catch (\Exception $e) {
                DB::rollBack();
                throw new \Exception('Ошибка записи в базу');
            }

            $message = 'Бренды успешно загружены';
        }

        return response()->json([
            'status' => true,
            'message' => $message
        ]);
    }

    public function saveCategory()
    {
        $rawData = $this->parserService->getCategories();

        $message = 'Нет данных в апи парсера';

        if (count($rawData) > 0) {
            try {
                DB::beginTransaction();
                Category::upsert(
                    $rawData,
                    []);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Ошибка записи в базу'
                ]);
            }

            $message = 'Категории успешно загружены';
        }

        return response()->json([
            'status' => true,
            'message' => $message
        ]);
    }

    public function saveCountry()
    {
        $rawData = $this->parserService->getCountries();

        $message = 'Нет данных в апи парсера';

        if (count($rawData) > 0) {
            try {
                DB::beginTransaction();
                Country::upsert(
                    $rawData,
                    []);
                DB::commit();

            } catch (\Exception $e) {
                DB::rollBack();
                throw new \Exception('Ошибка записи в базу');
            }

            $message = 'Страны успешно загружены';
        }

        return response()->json([
            'status' => true,
            'message' => $message
        ]);
    }


    public function saveSku()
    {
        set_time_limit(72000);

        $rawData = $this->parserService->getSkus();

        $skuIdsToChangeUploadStatus = [];

        $message = 'Нет данных в апи парсера';

        if (count($rawData) > 0) {
            $destinationFolder = 'public/image/sku/';

            foreach ($rawData as &$sku) {
                $images = json_decode($sku['images'], true);

                $isLoadImages = false;
                if ((int) $sku['images_upload_status'] === 0) {
                    $skuIdsToChangeUploadStatus[] = $sku['id'];
                    $isLoadImages = true;
                }

                    $currentImages = [];
                foreach ($images as &$image) {
                    $imagePathParts = explode('/', $image);
                    $imageName = $imagePathParts[count($imagePathParts) - 1];


                    $sourcePath = config('parser.parser_url') . '/' . $image;

                    if ($isLoadImages) {
                        ImageLoadingJob::dispatch($sourcePath, $destinationFolder, $imageName);
                    }

                    $currentImages[] = Storage::url($destinationFolder . $imageName);
                }

                unset($sku['images_upload_status']);

                $sku['images'] = json_encode($currentImages);
            }



            try {
                DB::beginTransaction();
                Sku::upsert(
                    $rawData,
                    []);
                DB::commit();

            } catch (\Throwable $e) {
                DB::rollBack();
                $m = 'Ошибка записи в базу. ' . $e->getMessage();
                throw new \Exception($m);
            }
            $this->parserService->changeSkuImagesUploadStatuses($skuIdsToChangeUploadStatus);
            $message = 'Товарные предложения  успешно загружены';
        }


        return response()->json([
            'status' => true,
            'message' => $message
        ]);
    }



    public function saveLink()
    {
        $rawData = $this->parserService->getLinks();
        $message = 'Нет данных в апи парсера';

        if (count($rawData) > 0) {
            try {
                DB::beginTransaction();
                Link::upsert(
                    $rawData,
                    []);
                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                throw new \Exception('Ошибка записи в базу' . $e->getMessage());
            }
            $message = 'Ссылки успешно загружены';
        }

        return response()->json([
            'status' => true,
            'message' => $message
        ]);
    }




    public function saveProduct()
    {
        $rawData = $this->parserService->getProducts();

        $message = 'Нет данных в апи парсера';

        if (count($rawData) > 0) {

            $chunks = array_chunk($rawData, 500);
            foreach ($chunks as $chunk) {
                try {
                    DB::beginTransaction();

                    Product::upsert(
                        $chunk,
                        []);

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    throw new \Exception('Ошибка записи в базу');
                }
            }
            $message = 'Продукты успешно загружены';
        }

        return response()->json([
            'status' => true,
            'message' => $message
        ]);
    }


    public function savePriceHistory()
    {
        $rawData = $this->parserService->getPriceHistory();

        $message = 'Нет данных в апи парсера';

        if (count($rawData) > 0) {

            $chunks = array_chunk($rawData, 500);
            foreach ($chunks as $chunk) {
                try {
                    DB::beginTransaction();

                    DB::statement("SET foreign_key_checks=0");
                    PriceHistory::upsert(
                        $chunk,
                        []);
                    DB::statement("SET foreign_key_checks=1");


                    DB::commit();
                } catch (Exception $e) {
                    DB::rollBack();
                    throw new \Exception('Ошибка записи в базу');
                }
            }

            $message = 'Цены успешно загружены';
        }


        return response()->json([
            'status' => true,
            'message' => $message
        ]);

    }


    public function saveCurrentPrice()
    {
        $rawData = $this->parserService->getCurrentPrice();

        $message = 'Нет данных в апи парсера';
        $skuIds = [];

        if (count($rawData) > 0) {

            $trackingSkuIds = Tracking::distinct()->select('sku_id')->get();

            $currentPricesBuSkuIds = SkuStore::select('price', 'link_id', 'sku_id')
                ->whereIn('sku_id', $trackingSkuIds)
                ->get();


            $pricesDown = array_filter($rawData, function ($updatedPrice) use ($currentPricesBuSkuIds) {
                foreach ($currentPricesBuSkuIds as &$currentPrice) {
                    if ($updatedPrice['sku_id'] === $currentPrice['sku_id'] &&
                        $updatedPrice['link_id'] === $currentPrice['link_id'] &&
                        $updatedPrice['price'] < $currentPrice['price']) {
                        unset($currentPrice);
                        return true;
                    }
                }
            });

            $skuIds = array_values(array_column($pricesDown, 'sku_id'));

            if (count($skuIds)) {
                PriceDropJob::dispatch($skuIds);
            }

            try {
                DB::beginTransaction();
                DB::statement("SET foreign_key_checks=0");

                SkuStore::upsert(
                    $rawData,
                    []);

//            SkuStore::truncate();
//            SkuStore::insert($rawData);

                DB::statement("SET foreign_key_checks=1");
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw new \Exception('Ошибка записи в базу');
            }


            $message = 'Текущие цены успешно загружены';
        }

        return response()->json([
            'status' => true,
            'message' => $message,
            'skuIds' => $skuIds,
        ]);
    }


    public function saveStore()
    {
        $rawData = $this->parserService->getStores();

        $message = 'Нет данных в апи парсера';

        if (count($rawData) > 0) {
            foreach ($rawData as $key => &$store) {
                if (!is_null($store['image'])) {
                    $store['image'] = '/storage' . $store['image'];
//                $imagePathParts = explode('/', $store['image']);
//                $imageName = $imagePathParts[count($imagePathParts) - 1];
//
//
//                $url = 'public/image/store/' . $imageName;
//
//                $fileContent = file_get_contents(env('PARSER_URL') . $store['image']);
//
//                Storage::put($url, $fileContent);
//                $publicUrl = Storage::url($url);
//
//
//                $store['image'] = $publicUrl;
                }
            }
            try {
                DB::beginTransaction();
                Store::upsert(
                    $rawData,
                    []);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw new \Exception('Ошибка записи в базу');
            }

            $message = 'Магазины успешно загружены';
        }

        return response()->json([
            'status' => true,
            'message' => $message
        ]);
    }

    public function saveIngredient()
    {
        $rawData = $this->parserService->getIngredients();

        $message = 'Нет данных в апи парсера';

        if (count($rawData) > 0) {
            try {
                DB::beginTransaction();
                Ingredient::upsert(
                    $rawData,
                    []);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw new \Exception('Ошибка записи в базу' . $e->getMessage());
            }
            $message = 'Ингредиенты успешно загружены';
        }

        return response()->json([
            'status' => true,
            'message' => $message
        ]);
    }

    public function saveIngredientProduct()
    {
        $rawData = $this->parserService->getIngredientProductTable();

        $message = 'Нет данных в апи парсера';

        if (count($rawData) > 0) {
            $chunks = array_chunk($rawData, 1000);
            foreach ($chunks as $chunk) {
                try {
                    DB::beginTransaction();
                    DB::table('ingredient_product')->upsert(
                        $chunk,
                        []);
                    DB::commit();

                } catch (\Exception $e) {
                    DB::rollBack();
                    throw new \Exception('Ошибка записи в базу');
                }
            }

            $message = 'Связывающая таблица ингредиентов и подуктов успешно загружена';
        }

        return response()->json([
            'status' => true,
            'message' => $message
        ]);
    }
}
