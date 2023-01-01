<?php

namespace App\Providers;


use App\Configuration;
use App\Services\Parser\ActualPriceParsingService;
use App\Services\Parser\PriceBulkInsertService;
use App\Services\Parser\PriceCrawlerService;
use App\Services\Parser\PriceParsingByCronService;
use App\Services\ProxyHttpClientService\ProxyHttpClientInterface;
use App\Services\ProxyHttpClientService\ProxyHttpClientService;
use App\Services\TreeService\TreeInterface;
use App\Services\TreeService\TreeService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(TreeInterface::class, TreeService::class);
        $this->app->singleton(ProxyHttpClientInterface::class, ProxyHttpClientService::class);

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
                    app(PriceCrawlerService::class)
                );
            }
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton(TreeInterface::class, TreeService::class);
        $this->app->singleton(Configuration::class);
    }
}
