<?php


namespace App\Services\ProxyHttpClientService;


interface ProxyServersInterface
{
    public function getList(): ?array;
}
