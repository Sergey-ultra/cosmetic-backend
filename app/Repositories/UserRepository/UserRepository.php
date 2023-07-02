<?php

namespace App\Repositories\UserRepository;

use App\Models\User;
use App\Models\UserInfo;
use App\Models\UserMessage;
use Illuminate\Support\Facades\DB;

class UserRepository
{
    const TECHNICAL_SUPPORT = 'Техническая поддержка';
    public function getChatsByUserId(int $userid): array
    {
        return UserMessage::query()
            ->select(
                sprintf('%s.id', UserMessage::TABLE),
                sprintf('%s.message', UserMessage::TABLE),
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
            )
            ->whereIn( sprintf('%s.id', UserMessage::TABLE), function ($query) use ($userid) {
                $query
                    ->selectRaw('Max(id)')
                    ->from(UserMessage::TABLE)
                    ->where('to_user', $userid)
                    ->orWhere('from_user', $userid)
                    ->groupBy(DB::raw('GREATEST(from_user, to_user), LEAST(from_user, to_user)'));
            })
            ->get()
            ->map(function(UserMessage $item) {
                return [
                    'id' => $item->id,
                    'message' => $item->message,
                    'user_name' => $item->user_name ?? self::TECHNICAL_SUPPORT,
                    'avatar' => $item->avatar ?? UserInfo::TECHNICAL_SUPPORT_AVATAR,
                    'data' => $item->data,
                    'type' => $item->type,
                    'created_at' => $item->created_at->format('Y-m-d'),
                ];
            })
            ->all();
    }
}
