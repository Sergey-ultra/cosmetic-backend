<?php

namespace App\Services\YMetricaService;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class YMetricaService implements IYMetrica
{
    public function __construct(protected Client $client){}

    public function getViews()
    {
        $response = $this->getData();
        $body = json_decode($response->getBody()->getContents(), true);
    }

    protected function getData(): ResponseInterface
    {
        $queryParameters = [
            'source' => 'visits',
            'date1' => now()->format('Y-m-d'),
            'date2' => now()->format('Y-m-d'),
        ];


        return $this->client->request(
            'GET',
            sprintf('%s?%s', config('yandex-metrica.base_url'), http_build_query($queryParameters)),
            [
                "header" => [
                    "Content-Type: application/x-yametrika+json",
                    sprintf("Authorization: OAuth %s", config('yandex-metrica.o_auth_token')),
                ],
            ]
        );
    }
}
