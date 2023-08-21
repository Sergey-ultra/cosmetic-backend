<?php

namespace App\Providers;


use App\Configuration;
use App\Repositories\ArticleRepository\ArticleRepositoryRepository;
use App\Repositories\ArticleRepository\IArticleRepository;
use App\Repositories\ReviewRepository\IReviewRepository;
use App\Repositories\ReviewRepository\ReviewRepository;
use App\Repositories\SkuRepository\ISkuRepository;
use App\Services\CompressImageService\CompressImageInterface;
use App\Services\CompressImageService\CompressImageService;
use App\Services\ImageLoadingService\ImageLoadingInterface;
use App\Services\ImageLoadingService\ImageLoadingService;
use App\Services\MessageService\MessageService;
use App\Services\MessageService\MessageServiceInterface;
use App\Services\Parser\ActualPriceParsingService;
use App\Services\Parser\Contracts\ILinkParser;
use App\Services\Parser\LinkCrawlerParser;
use App\Services\Parser\PriceBulkInsertService;
use App\Services\Parser\PriceCrawlerService;
use App\Services\Parser\PriceParsingByCronService;
use App\Services\PaymentService\IPayment;
use App\Services\PaymentService\YooMoneyService;
use App\Services\PriceHistoryService\PriceHistoryInterface;
use App\Services\PriceHistoryService\PriceHistoryService;
use App\Services\ProxyHttpClientService\Proxy6NetService;
use App\Services\ProxyHttpClientService\ProxyHttpClientInterface;
use App\Services\ProxyHttpClientService\ProxyHttpClientService;
use App\Services\ProxyHttpClientService\ProxyServersInterface;
use App\Services\TreeService\TreeInterface;
use App\Services\TreeService\TreeService;
use App\Services\UrlService\IUrlService;
use App\Services\UrlService\UrlService;
use App\Services\VideoSavingService\VideoSavingInterface;
use App\Services\VideoSavingService\VideoSavingService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(TreeInterface::class, TreeService::class);
        $this->app->singleton(ProxyServersInterface::class, Proxy6NetService::class);
        $this->app->singleton(ProxyHttpClientInterface::class, ProxyHttpClientService::class);
        $this->app->singleton(CompressImageInterface::class, CompressImageService::class);
        $this->app->singleton(VideoSavingInterface::class, VideoSavingService::class);
        $this->app->singleton(PriceHistoryInterface::class, PriceHistoryService::class);
        $this->app->singleton(ILinkParser::class, LinkCrawlerParser::class);
        $this->app->singleton(IUrlService::class, UrlService::class);
        $this->app->singleton(ImageLoadingInterface::class, ImageLoadingService::class);
        $this->app->singleton(IReviewRepository::class, ReviewRepository::class);
        $this->app->singleton(IArticleRepository::class, ArticleRepositoryRepository::class);
        $this->app->singleton(ISkuRepository::class, ISkuRepository::class);
        $this->app->singleton(IPayment::class, YooMoneyService::class);
        $this->app->singleton(MessageServiceInterface::class, MessageService::class);

        $this->app->bind(
            PriceCrawlerService::class,
            function($app) {
                return new PriceCrawlerService(app(ProxyHttpClientInterface::class));
            }
        );

        $this->app->singleton(
            PriceParsingByCronService::class,
            static function ($app) {
                return new PriceParsingByCronService(
                    $app->make(ActualPriceParsingService::class),
                    $app->make(Configuration::class),
                    $app->make(PriceBulkInsertService::class),
                    $app->make(PriceCrawlerService::class)
                );
            }
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->app->singleton(TreeInterface::class, TreeService::class);
        $this->app->singleton(Configuration::class);
    }
}
