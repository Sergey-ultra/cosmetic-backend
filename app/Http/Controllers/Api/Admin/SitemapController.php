<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Crawler\Crawler;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;


class SitemapController extends Controller
{
    /**
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(): JsonResponse
    {
        set_time_limit(30000);

        $log = "";

        $sitemap = SitemapGenerator::create('https://smart-beautiful.ru/catalog/umjivanie')
            //->getSitemap()
            ->hasCrawled(function (Url $url)  {
//                $log .= $url->url . "\r\n";


                if ($url->segment(1) === 'to') {
                    return;
                }
                return $url;
            })
        ;

        //$sitemap->add(Url::create('/product/')->setPriority(1));

//            ->configureCrawler(function (Crawler $crawler) {
//                $crawler->setMaximumDepth(7);
//            })
        $sitemap->writeToFile('sitemap.xml');
        file_put_contents('urls.txt', json_decode($sitemap));

        return response()->json([
            'data' =>
                [
                    'status' => true,
                    'message' => 'Карта сайта создана',

                ]
        ]);
    }
}
