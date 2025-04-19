<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MessageRequest;
use App\Jobs\AdminNotificationJob;
use App\Models\User;
use App\Models\UserInfo;
use App\Models\UserMessage;
use App\Repositories\MessageRepository\MessageRepository;
use App\Services\MessageService\MessageServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends Controller
{
    public function myMessages(MessageRepository $messageRepository): JsonResponse
    {
        $userId = Auth::guard('api')->id();
        $result = $messageRepository->getChats($userId);

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

        $result = $messageRepository->getAllMessagesByUserId($userId, $dialogUserId);

        return response()->json(['data' =>
            [
                'messages' => $result,
                'dialog_user' => $dialogUserName,
                'dialog_avatar' => $dialogUserAvatar,
                'dialog_user_id' => $dialogUserId,
            ]
        ]);
    }

    public function sendMessage(MessageRequest $request, MessageServiceInterface $messageService): JsonResponse
    {
        $new = $messageService->sendMessage(
            Auth::guard('api')->id(),
            $request->input('message'),
            $request->input('dialog_user_id')
        );

        if ($new['type'] === 'feedback' && app()->environment(['production'])) {
            $message = sprintf("Добавлено новое сообщение в техподдержку с id %d", $new['id']);
            AdminNotificationJob::dispatch($message);
        }

        return response()->json(['data' => $new], Response::HTTP_CREATED);
    }
}
