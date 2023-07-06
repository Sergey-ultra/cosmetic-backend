<?php


namespace App\Services\ProxyHttpClientService;


use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Psr\Http\Message\ResponseInterface;

class ProxyHttpClientService implements ProxyHttpClientInterface
{
    public const USER_AGENTS = [
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36",
        "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:95.0) Gecko/20100101 Firefox/95.0"
    ];

    protected ?string $currentProxy = null;

    protected int $currentRequestAfterChangingProxy = 0;

    protected int $requestsCountBeforeChangingProxy = 0;

    protected array $proxyList = [];

    public function __construct(protected Client $httpClient, protected ProxyServersInterface $proxyServersService)
    {}

    public function setRequestsCountBeforeChangingProxy(int $value): void
    {
        $this->requestsCountBeforeChangingProxy = $value;
    }

    public function request(string $link, array $options = [], bool $isUseProxy = false): ResponseInterface
    {
        $requestOptions = [
            'http_errors' => false,
            'headers' => [
                'User-Agent' => $this->randomUserAgent(),
            ]
        ];
        if (count($options)) {
            $requestOptions = array_merge($requestOptions, $options);
        }

        if ($isUseProxy) {
            $this->proxyList = $this->getProxyList();
            if (count($this->proxyList)) {
                $this->setCurrentProxy();

                if ($this->currentProxy) {
                    $requestOptions['proxy'] = $this->currentProxy;
                }
            }
        }

        return $this->httpClient->request('GET', $link, $requestOptions);
    }

    protected function randomUserAgent(): string
    {
        return self::USER_AGENTS[rand(0, count(self::USER_AGENTS) - 1)];
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
            static function (): array {
                return $this->proxyServersService->getList() ?? [];
            }
        );
    }
}
