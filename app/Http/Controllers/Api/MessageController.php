<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MessageRequest;
use App\Models\User;
use App\Models\UserInfo;
use App\Models\UserMessage;
use App\Repositories\MessageRepository\MessageRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends Controller
{
    public function myMessages(MessageRepository $messageRepository): JsonResponse
    {
        $userId = Auth::guard('api')->id();
        $result = $messageRepository->getLastTechSupportMessagesByUserId($userId);

        return response()->json(['data' => $result]);
    }

    public function chat(MessageRepository $messageRepository, int $lastMessageId): JsonResponse
    {
        $userId = Auth::guard('api')->id();
        $lastMessage = UserMessage::query()->find($lastMessageId);
        if (!$lastMessage) {
            return response()->json([], Response::HTTP_NOT_FOUND);
        }
        $dialogUserId = $lastMessage->from_user !== $userId
            ? $lastMessage->from_user
            : $lastMessage->to_user;

        $dialogUserName = MessageRepository::TECHNICAL_SUPPORT;
        $dialogUserAvatar = UserInfo::TECHNICAL_SUPPORT_AVATAR;

        if (null !== $dialogUserId) {
            $dialogUser = $messageRepository->getDialogUser($dialogUserId);
            $dialogUserName = $dialogUser->user_name;
            $dialogUserAvatar = $dialogUser->avatar;
        }

        $result = $messageRepository->getAllTechSupportMessagesByUserId($userId, $dialogUserId);

        return response()->json(['data' =>
            [
                'messages' => $result,
                'dialog_user' => $dialogUserName,
                'dialog_avatar' => $dialogUserAvatar,
                'dialog_user_id' => $dialogUserId,
            ]
        ]);
    }

    public function sendMessage(MessageRequest $request): JsonResponse
    {
        $userId = Auth::guard('api')->id();
        $params = [
            'message' => $request->input('message'),
            'from_user' => $userId,
        ];
        if ($request->input('dialog_user_id')) {
            $params['to_user'] = $request->input('dialog_user_id');
        } else {
            $params['type'] = 'feedback';
        }

        $new = UserMessage::query()->create($params);

        $new->load('user.info');

        $result = [
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

        return response()->json(['data' => $result], Response::HTTP_CREATED);
    }
}
