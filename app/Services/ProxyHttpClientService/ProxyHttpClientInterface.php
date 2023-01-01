<?php


namespace App\Services\ProxyHttpClientService;


use Psr\Http\Message\ResponseInterface;

interface ProxyHttpClientInterface
{
    public function request(string $link): ResponseInterface;
}
