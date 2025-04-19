<?php

namespace App\Services\MessageService;

use Illuminate\Database\Eloquent\Model;

interface MessageServiceInterface
{
    public function addSku(string $skuCode, string $name, int $userId): ?Model;
    public function sendMessage(?int $myUserId, ?string $message, ?int $dialogUserId): array;
}
