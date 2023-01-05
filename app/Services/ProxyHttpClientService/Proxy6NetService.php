<?php


namespace App\Services\ProxyHttpClientService;


use GuzzleHttp\Client;

class Proxy6NetService implements ProxyServersInterface
{
    public function __construct(protected Client $httpClient){}

    public function getList(): ?array
    {
        try {
            $response = $this->httpClient->request('GET', config('proxyapi.proxy6_net_url'));
            $body = json_decode($response->getBody()->getContents(), true);

            if ('yes' === $body['status']) {
                $list = $body['list'];
                return array_map(
                    function (array $item) {
                        //"http://username:password@192.168.16.1:10"
                        return "{$item['type']}://{$item['user']}:{$item['pass']}@{$item['host']}";
                    },
                    $list
                );
            }

            return null;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
