<?php

namespace App\Services\TelegramApiService;

use GuzzleHttp\Client;

class TelegramAdminNotificationApiService extends TelegramApiService
{
    public function __construct(Client $client)
    {
        parent::__construct($client);
        $this->setBaseUri(config('telegrambot.admin_notification_api_url'));
    }
}
