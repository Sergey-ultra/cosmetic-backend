<?php

declare(strict_types=1);


namespace App\Services;


use GuzzleHttp\Client;

final class ParserService
{
    private Client $client;
    private $token;

//    const PATH = __DIR__ . '/../../token.txt';
//
//    public function __construct(Client $client)
//    {
//        $this->client = $client;
//        $this->login();
//    }

//    private function login(): void
//    {
//        $token = file_get_contents(self::PATH);
//
//
//        if (!$token) {
//            $response = $this->client->request(
//                'POST',
//                config('parser.login_url'),
//                [
//                    'json' => [
//                        'email' => config('parser.email'),
//                        'password' => config('parser.password')
//                    ]
//                ]
//            );
//
//
//            $token = json_decode($response->getBody()->getContents(), true)['token'];
//            file_put_contents(self::PATH, $token);
//        }
//
//        $this->token = $token;
//    }

    private function getData(string $url): array
    {

        $response = $this->client->request(
            'GET',
            $url,
            [
                'headers' => [
                    'Authorization' => 'Bearer ' .  $this->token,
                    'Accept' => 'application/json',
                ]
            ]
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    public function changeSkuImagesUploadStatuses(array $ids): void
    {
        $this->client->request(
            'POST',
            config('parser.change_sku_images_upload_statuses'),
            [
                'headers' => [
                    'Authorization' => 'Bearer ' .  $this->token,
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'ids' => $ids
                ]
            ]
        );
    }

    public function getBrands(): array
    {
       return $this->getData(config('parser.brand_url'));
    }

    public function getCategories(): array
    {
        return $this->getData(config('parser.category_url'));
    }

    public function getCountries(): array
    {
        return $this->getData( config('parser.country_url'));
    }

    public function getSkus(): array
    {
        return $this->getData(config('parser.sku_url'));
    }

    public function getLinks(): array
    {
        return $this->getData(config('parser.link_url'));
    }

    public function getProducts():array
    {
        return $this->getData(config('parser.product_url'));
    }

    public function getPriceHistory(): array
    {
        return $this->getData(config('parser.price_history_url'));
    }

    public function getCurrentPrice(): array
    {
        return $this->getData( config('parser.current_price_url'));
    }

    public function getStores(): array
    {
        return $this->getData(config('parser.store_url'));
    }

    public function getIngredients(): array
    {
        return $this->getData(config('parser.ingredient_url'));
    }

    public function getIngredientProductTable(): array
    {
        return $this->getData(config('parser.ingredient_product_url'));
    }

}