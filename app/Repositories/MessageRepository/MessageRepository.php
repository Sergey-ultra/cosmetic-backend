<?php

namespace App\Repositories\MessageRepository;

use App\Models\User;
use App\Models\UserInfo;
use App\Models\UserMessage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class MessageRepository
{
    const TECHNICAL_SUPPORT = 'Техническая поддержка';
    public function getLastMessagesByUserId(?int $myUserId): array
    {
        return $this->getMessageQuery()
            ->whereIn(sprintf('%s.id', UserMessage::TABLE), function ($query) use ($myUserId) {
                $query
                    ->selectRaw('Max(id)')
                    ->from(UserMessage::TABLE)
                    ->where('to_user', $myUserId)
                    ->orWhere('from_user', $myUserId)
                    ->groupBy('chat');
            })
            ->get()
            ->map($this->getMapper($myUserId))
            ->all();
    }

    public function getAllTechSupportMessagesByUserId(?int $myUserId, ?int $dialogUserId): array
    {
        return $this->getMessageQuery()
            ->where(function ($query) use ($myUserId, $dialogUserId) {
                $query->where('to_user', $myUserId)->where('from_user', $dialogUserId);
            })
            ->orWhere(function($query) use ($myUserId, $dialogUserId) {
                $query->where('to_user', $dialogUserId)->where('from_user', $myUserId);
            })
            ->get()
            ->map($this->getMapper($myUserId))
            ->all();
    }

    private function getMessageQuery(): Builder
    {
        return UserMessage::query()
            ->select(
                sprintf('%s.id', UserMessage::TABLE),
                sprintf('%s.message', UserMessage::TABLE),
                sprintf('%s.from_user', UserMessage::TABLE),
                sprintf('%s.name AS user_name', User::TABLE),
                sprintf('%s.data', UserMessage::TABLE),
                sprintf('%s.type', UserMessage::TABLE),
                sprintf('%s.created_at', UserMessage::TABLE),
                sprintf('%s.avatar', UserInfo::TABLE),
            )
            ->leftJoin(
                User::TABLE,
                sprintf('%s.id', User::TABLE),
                '=',
                sprintf('%s.from_user', UserMessage::TABLE)
            )
            ->leftJoin(
                UserInfo::TABLE,
                sprintf('%s.user_id', UserInfo::TABLE),
                '=',
                sprintf('%s.from_user', UserMessage::TABLE)
            );
    }

    private function getMapper(?int $userId): callable
    {
        return function(UserMessage $item) use($userId) {
            return [
                'id' => $item->id,
                'message' => $item->message,
                'is_mine' => $item->from_user === $userId,
                'user_name' => $item->user_name ?? self::TECHNICAL_SUPPORT,
                'avatar' => $item->avatar ?? ($item->from_user === null
                        ? UserInfo::TECHNICAL_SUPPORT_AVATAR
                        : UserInfo::DEFAULT_AVATAR),
                'data' => $item->data,
                'type' => $item->type,
                'created_at' => $item->created_at->format('Y-m-d') === now()->format('Y-m-d')
                    ? sprintf('Сегодня в %s', $item->created_at->format('H-i'))
                    : $item->created_at->format('Y-m-d'),
            ];
        };
    }

    public function getDialogUser(int $dialogUserId): ?User
    {
        return User::query()
            ->select(
                sprintf('%s.name AS user_name', User::TABLE),
                sprintf('%s.avatar', UserInfo::TABLE),
            )
            ->leftJoin(
                UserInfo::TABLE,
                sprintf('%s.user_id', UserInfo::TABLE),
                '=',
                sprintf('%s.id', User::TABLE)
            )
            ->where(sprintf('%s.id', User::TABLE), $dialogUserId)
            ->first();
    }
}
