<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\DataProviderWithDTO;
use App\Http\Controllers\Traits\ParamsDTO;
use App\Http\Requests\MessageRequest;
use App\Jobs\AdminNotificationJob;
use App\Models\UserInfo;
use App\Models\UserMessage;
use App\Repositories\MessageRepository\MessageRepository;
use App\Services\MessageService\MessageServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;


class MessageController extends Controller
{
    use DataProviderWithDTO;
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page') ?? 10;

        $paramsDto = new ParamsDTO(
            $request->input('filter', []),
            $request->input('sort', ''),
        );

        $query = UserMessage::query()->whereNull('to_user');

        $result = $this->prepareModel($paramsDto, $query)->paginate($perPage);

        return response()->json(['data' => $result]);
    }

    public function adminMessage(MessageRepository $messageRepository): JsonResponse
    {
        $result = $messageRepository->getSupportChats();

        return response()->json(['data' => $result]);
    }

    public function chat(MessageRepository $messageRepository, int $lastMessageId): JsonResponse
    {

        $lastMessage = UserMessage::query()->find($lastMessageId);
        if (!$lastMessage) {
            return response()->json([], Response::HTTP_NOT_FOUND);
        }
        $dialogUserId = $lastMessage->to_user ?? $lastMessage->from_user;


        $dialogUserName = MessageRepository::TECHNICAL_SUPPORT;
        $dialogUserAvatar = UserInfo::TECHNICAL_SUPPORT_AVATAR;

        if (null !== $dialogUserId) {
            $dialogUser = $messageRepository->getDialogUser($dialogUserId);
            $dialogUserName = $dialogUser->user_name;
            $dialogUserAvatar = $dialogUser?->avatar ?? UserInfo::DEFAULT_AVATAR;
        }

        $result = $messageRepository->getAllMessagesByUserId(null, $dialogUserId);

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
        $result = $messageService->sendMessage(
            Auth::guard('api')->id(),
            $request->input('message'),
            $request->input('dialog_user_id')
        );

        return response()->json(['data' => $result], Response::HTTP_CREATED);
    }
}
