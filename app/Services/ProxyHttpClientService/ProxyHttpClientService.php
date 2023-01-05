<?php


namespace App\Services\ProxyHttpClientService;


use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Psr\Http\Message\ResponseInterface;

class ProxyHttpClientService implements ProxyHttpClientInterface
{
    protected ?string $currentProxy = null;

    protected int $currentRequestAfterChangingProxy = 0;

    protected int $requestsCountBeforeChangingProxy = 0;

    protected array $proxyList = [];

    public function __construct(protected Client $httpClient, protected Proxy6NetService $proxyServersService)
    {
        $this->proxyList = $this->getProxyList();
    }

    public function setRequestsCountBeforeChangingProxy(int $value): void
    {
        $this->requestsCountBeforeChangingProxy = $value;
    }

    public function request(string $link): ResponseInterface
    {
        $options =  ['http_errors' => false];

        if (count($this->proxyList)) {
            $this->setCurrentProxy();

            if ($this->currentProxy) {
                $options['proxy'] = $this->currentProxy;
            }
        }


        return $this->httpClient->request('GET', $link, $options);
    }

    protected function setCurrentProxy(): void
    {
        if ($this->requestsCountBeforeChangingProxy === 0) {
            $this->currentProxy = $this->getRandomProxy();
        } else {
            if ($this->currentRequestAfterChangingProxy < $this->requestsCountBeforeChangingProxy) {
                ++$this->currentRequestAfterChangingProxy;
            } else {
                $this->currentRequestAfterChangingProxy = 0;
                $this->currentProxy = $this->getRandomProxy();
            }
        }
    }

    protected function getRandomProxy(): ?string
    {
        if (!count($this->proxyList)) {
            return null;
        }

        return $this->proxyList[rand(0, count($this->proxyList) - 1)];
    }

    protected function getProxyList(): array
    {
        return Cache::remember(
            'proxy_servers',
            now()->addDay(),
            function (): array {
                $list = $this->proxyServersService->getList();
                return $list ?? [];
            }
        );
    }
}
