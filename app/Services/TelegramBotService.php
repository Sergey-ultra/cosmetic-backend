<?php

declare(strict_types=1);


namespace App\Services;


use GuzzleHttp\Client;

final class TelegramBotService
{


    public function __construct(private Client $client)
    {}

    public function sendMessage($chatId, string $message)
    {
        return $this->client->request(
            'POST',
            config('telegrambot.url').'/sendMessage',
            [
                'form_params' => [
                    'chat_id' => $chatId,
                    'text' => $message
                ]
            ]
        );
    }
}