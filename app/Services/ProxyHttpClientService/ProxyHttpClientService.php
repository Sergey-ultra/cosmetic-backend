<?php


namespace App\Services\ProxyHttpClientService;


use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Psr\Http\Message\ResponseInterface;

class ProxyHttpClientService implements ProxyHttpClientInterface
{
    public function __construct(protected Client $httpClient, protected Proxy6NetService $proxyServersService){}

    public function request(string $link): ResponseInterface
    {
        $options =  ['http_errors' => false];

        if ($proxy = $this->getRandomProxy()) {
            $options['proxy'] = $proxy;
        }

        return $this->httpClient->request('GET', $link, $options);
    }

    protected function getRandomProxy(): ?string
    {
        $proxyServers = Cache::remember(
            'proxy_servers',
            now()->addDay(),
            function (): array {
                $list = $this->proxyServersService->getList();
                return $list ?? [];
            }
        );

        if (!count($proxyServers)) {
            return null;
        }

        return $proxyServers[rand(0, count($proxyServers) - 1)];
    }
}
