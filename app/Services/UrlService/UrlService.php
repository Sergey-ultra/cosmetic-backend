<?php

namespace App\Services\UrlService;

class UrlService implements IUrlService
{
    public function relativeUrlToAbsolute(string $relativeUrl, string $base): string
    {
        /* return if already absolute URL */
        if (parse_url($relativeUrl, PHP_URL_SCHEME) != '') {
            return $relativeUrl;
        }

        /* queries and anchors */
        if ($relativeUrl[0]=='#' || $relativeUrl[0]=='?') return $base.$relativeUrl;

        /* parse base URL and convert to local variables:
           $scheme, $host, $path */
        $urlObj = (object)(parse_url($base));


        /* dirty absolute URL */
        $absolute = "{$urlObj->host}$relativeUrl";
        //$absolute = $this->clearAbsoluteUrl($absolute);

        return $urlObj->scheme.'://'.$absolute;
    }

    protected function clearAbsoluteUrl(string $absolute): string
    {
        /* replace '//' or '/./' or '/foo/../' with '/' */
        $regex = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');
        for($n=1; $n>0; $absolute=preg_replace($regex, '/', $absolute, -1, $n)) {}

        return $absolute;
    }
}
