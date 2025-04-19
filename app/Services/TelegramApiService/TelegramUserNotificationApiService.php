<?php

namespace App\Services\TelegramApiService;

use GuzzleHttp\Client;

class TelegramUserNotificationApiService extends TelegramApiService
{

    public function __construct(Client $client)
    {
        parent::__construct($client);
        $this->setBaseUri(config('telegrambot.user_notification_api_url'));
    }
}
