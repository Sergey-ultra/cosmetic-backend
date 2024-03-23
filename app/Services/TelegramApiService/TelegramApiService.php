<?php

declare(strict_types=1);

namespace App\Services\TelegramApiService;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class TelegramApiService
{
    protected string $baseUri;
    public function __construct(protected Client $client){}


    public function sendMessage($chatId, string $message): ResponseInterface
    {
        return $this->request(
            'POST',
            '/sendMessage',
            [
                'form_params' => [
                    'chat_id' => $chatId,
                    'text' => $message
                ]
            ]
        );
    }

    protected function request(string $method, string $uri = '', array $options = []): ResponseInterface
    {
        return $this->client->request(
            $method,
            $this->baseUri . $uri,
            $options
        );
    }

    protected function setBaseUri(string $baseUri): void
    {
        $this->baseUri = $baseUri;
    }
}
