<?php

namespace App\Services\UserLocationService;

use GuzzleHttp\Client;

class UserLocationService
{
    public function __construct(protected Client $httpClient){}

    public function getLocationByIp(string $ip): array
    {
        $response = $this->httpClient->request('GET', "http://ip-api.com/json/$ip");
        return json_decode($response->getBody()->getContents(), true);
    }
}
