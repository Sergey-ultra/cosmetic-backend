<?php

namespace App\Services\TelegramApiService;

use GuzzleHttp\Client;

class TelegramUserNotificationApiService extends TelegramApiService
{

    public function __construct()
    {
        $this->client = new Client([
                'base_uri' => config('telegrambot.user_notification_api_url'),
                'timeout' => 120,
                'connect_timeout' => 3,
            ]
        );
    }
}
