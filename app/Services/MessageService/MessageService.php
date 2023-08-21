<?php

namespace App\Services\MessageService;

use App\Models\UserInfo;
use App\Models\UserMessage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MessageService implements MessageServiceInterface
{

    public function addSku(string $skuCode, string $name, int $userId): ?Model
    {
        return UserMessage::query()->create([
            'data' => [
                'sku_code' => $skuCode,
                'name' => $name,
            ],
            'chat' => $this->generateChatUuid($userId, null),
            'type' => 'add-sku',
            'to_user' => $userId,
        ]);
    }

    public function sendMessage(?int $myUserId, ?string $message, ?int $dialogUserId): array
    {
        $params = [
            'message' => $message,
            'from_user' => $myUserId,
        ];

        if ($dialogUserId) {
            $params['to_user'] = $dialogUserId;
        } else {
            $params['to_user'] = null;
            $params['type'] = 'feedback';
        }

        $params['chat'] = $this->generateChatUuid($myUserId, $dialogUserId);

        $new = UserMessage::query()->create($params);



        $new->load('user.info');

        return [
            'id' => $new->id,
            'message' => $new->message,
            'is_mine' => true,
            'user_name' => $new->user->name ,
            'avatar' => $new->user->info->avatar ?? UserInfo::DEFAULT_AVATAR,
            'type' => $new->type,
            'created_at' => $new->created_at->format('Y-m-d') === now()->format('Y-m-d')
                ? sprintf('Сегодня в %s', $new->created_at->format('H-i'))
                : $new->created_at->format('Y-m-d'),
        ];
    }

    protected function generateChatUuid(?int $myUserId, ?int $dialogUserId): string
    {
        $existingDialogMessage = UserMessage::query()
            ->where(function ($query) use ($myUserId, $dialogUserId) {
                $query->where('to_user', $myUserId)->where('from_user', $dialogUserId);
            })
            ->orWhere(function ($query) use ($myUserId, $dialogUserId) {
                $query->where('to_user', $dialogUserId)->where('from_user', $myUserId);
            })
            ->first();

        if ($existingDialogMessage && $existingDialogMessage->chat) {
            return $existingDialogMessage->chat;
        }
        return Str::uuid()->toString();
    }
}
