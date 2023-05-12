<?php

declare(strict_types=1);

namespace App\Services\TelegramApiService;

use GuzzleHttp\Client;

abstract class TelegramApiService
{
    protected Client $client;

//    private Client $client;
    abstract public function __construct();
//    {
//        $this->client = new Client([
//            'base_uri' => config('telegrambot.admin_notification_api_url'),
//            'timeout' => 120,
//            'connect_timeout' => 3,
//                ]
//        );
 //   }

    public function sendMessage($chatId, string $message)
    {
        return $this->client->request(
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
}
