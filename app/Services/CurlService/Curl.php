<?php

declare(strict_types=1);

namespace App\Services\CurlService;


use App\Services\ProxyServersService\ProxyServersInterface;
use App\Services\ProxyServersService\ProxyServersService;

class Curl
{
    //private const USER_AGENTS_FILE = __DIR__ . "/../config/user-agents.json";




    public function request(string $url, int $timeout = 12, int $connecttimeout = 10, array $startHeaders = []): CurlResponse
    {

        $domain = parse_url($url)['host'];
        $domain = preg_replace('#www#','', $domain);

        $userAgents =  [
            "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36",
            "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:95.0) Gecko/20100101 Firefox/95.0"
        ];

        //$userAgents = array_merge($userAgents, JsonFile::read(self::USER_AGENTS_FILE));
        // "Host: $domain",
//        $startHeaders = [
//            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8",
//            "Accept-Encoding: gzip, deflate, br",
//            "Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3",
//            "Cache-Control: no-cache",
//            "Connection: keep-alive",
//            //"Cookie: _userGUID=0:kzd6w9gi:_eP3V0esXEVUX6GXS_SBBRzzvy0ivYjR; rrpvid=911748116019313; rcuid=62018b32cc1cff0001c2c509; _ga=GA1.2.558427357.1644268307; _ym_uid=1640989169655177452; _ym_d=1644268308; _fbp=fb.1.1644268308076.1234559884; PHPSESSID=2538cb88087c41fda1334310eab99923; default=1983a1d03e154b924c1dfd17129d10ff; language=ru-ru; currency=RUB; _dvs=0:l0vdg24x:zo49sY4keesUu4jXbfGXJQ5GIkHRtfQx; _gid=GA1.2.983791798.1647544482; _ym_isad=2; _ym_visorc=w; register-popup=true; dSesn=94b7ba03-5c89-da6a-68de-57f53319a66f",
//
////            "Pragma: no-cache",
////            "Sec-Fetch-Dest: document",
////            "Sec-Fetch-Mode: navigate",
////            "Sec-Fetch-Site: same-origin",
////            "Sec-Fetch-User: ?1",
////            "Upgrade-Insecure-Requests: 1"
//
//        ];

        $ch = curl_init();



//        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false);
//        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
//        curl_setopt($this->ch, CURLOPT_COOKIEFILE, 'user_cookie_file.txt');
//        curl_setopt($this->ch, CURLOPT_COOKIEJAR, 'user_cookie_file.txt');
//        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
//        curl_setopt($this->ch, CURLOPT_REFERER, PAGEAUTHORIZATION);

        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $startHeaders);
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgents[rand(0, count($userAgents) - 1)]);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connecttimeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

//        if ($currentProxy = $this->proxyServersService->getRandomProxy()) {
//            curl_setopt($ch, CURLOPT_PROXY, $currentProxy);
//        }

        curl_setopt($ch, CURLOPT_HEADER, true); //получить заголовки ответа
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);

        try {
            $response = curl_exec($ch);
            //dd($response);

            $curlResponse = new CurlResponse();
            $curlResponse->setCode((int)curl_getinfo($ch, CURLINFO_HTTP_CODE));

            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

            $curlResponse->setHeader(substr($response, 0, $header_size));
            $curlResponse->setBody(substr($response, $header_size));
            $curlResponse->setErrors(curl_error($ch));

            return $curlResponse;
        } catch (\Throwable $e) {

        } finally {
            curl_close($ch);
        }

    }
}
