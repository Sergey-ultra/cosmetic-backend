<?php
return [
    'admin_ip' => env('ADMIN_IP_ADDRESS'),
    'admin_notification_api_url' => 'https://api.telegram.org/bot' . env('TELEGRAM_ADMIN_NOTIFICATION_API_KEY'),
    'admin_notification_url' => env('TELEGRAM_ADMIN_NOTIFICATION_URL'),
    'user_notification_api_url' => 'https://api.tlgr.org/bot' . env('TELEGRAM_USER_NOTIFICATION_API_KEY') . '/',
    'user_notification_url' => env('TELEGRAM_USER_NOTIFICATION_URL'),
    'admin_user_id' => env('TELEGRAM_ADMIN_USER_ID'),
];
