<?php

namespace App\Services\TelegramApiService;

use GuzzleHttp\Client;

class TelegramAdminNotificationApiService extends TelegramApiService
{
    public function __construct()
    {
        $this->client = new Client([
                'base_uri' => config('telegrambot.admin_notification_api_url'),
                'timeout' => 120,
                'connect_timeout' => 3,
            ]
        );
    }
}
