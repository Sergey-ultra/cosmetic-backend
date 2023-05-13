<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserTelegramInfo;
use App\Services\TelegramApiService\TelegramUserNotificationApiService;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use ReflectionClass;

class TelegramController extends Controller
{
    public function startNotificationBot(): JsonResponse
    {
        $user = Auth::user();
        if (!$user->telegramInfo) {
            $hash = Str::uuid();
            $user->telegramInfo()->updateOrCreate([],['hash' => $hash]);
        } else {
            $hash = $user->telegramInfo->hash;
        }


        $qrCode = '';
        $botUrl = config('telegrambot.user_notification_url') . '?start=' . $hash;
        try {
            $qrCode = QrCode::size(200)
                ->backgroundColor(255, 255, 0)
                ->color(0, 0, 255)
                ->margin(1)
                ->generate($botUrl);
        } catch (\Throwable $e) {
            $qrCode = null;
        }

        return response()->json([
            'status' => true,
            'data' => [
                'qr_code' => $qrCode,
                'bot_url' => $botUrl
            ]
        ]);
    }

    public function updateTelegramUser(Request $request, TelegramUserNotificationApiService $telegramUserNotificationApiService): void
    {
        $params = $request->all();

        if (preg_match('/[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}/', $params['message']['text'], $matches)) {

            $hash = $matches[0];
            if (strlen($hash) === 36) {
                $telegramUserName = $params['message']['chat']['first_name'];
                $telegramUserId = $params['message']['chat']['id'];

                $currentTelegramUser = UserTelegramInfo::query()->where('hash', $hash)->first();
                if ($currentTelegramUser) {
                    $currentTelegramUser->update([
                        'telegram_user_name' => $telegramUserName,
                        'telegram_user_id' => $telegramUserId,
                    ]);
                    $userEmail = $currentTelegramUser->user->email;
                    $message = "Готово! Аккаунт $userEmail синхронизирован 👌

Теперь вы будете получать уведомления от Smart Beautiful прямо в этом чате";
                    $telegramUserNotificationApiService->sendMessage($telegramUserId, $message);
                }

            }
        }
    }

    public function unsubscribe(Request $request, TelegramUserNotificationApiService $telegramUserNotificationApiService): JsonResponse|null
    {
        if ($request->input('hash') && $request->input('code')) {
            $currentTelegramUser = UserTelegramInfo::query()->where(['unsubscribe_code' => $request->input('code'), 'hash' => $request->input('hash')])->first();
            $currentTelegramUser->delete();
            return response()->json([]);
        } else {
            $user = Auth::user();
            if (isset($user->telegramInfo->hash) && isset($user->telegramInfo->telegram_user_id)) {
                $unsubscribeCode = mt_rand(1111, 9999);
                $user->telegramInfo->update(['unsubscribe_code' => $unsubscribeCode]);

                try {
                    $message = $unsubscribeCode . ' - код для подтверждения отключения уведомлений в Телеграм';
                    $response = $telegramUserNotificationApiService->sendMessage($user->telegramInfo->telegram_user_id, $message);
                    $statusCode = $response->getStatusCode();
                    Log::notice($statusCode);
                } catch (ClientException $e) {
                    return response()->json(['message' => $e]);
                }
                return response()->json(['data' => ['hash' => $user->telegramInfo->hash]]);
            }
        }
    }

    public function account(): JsonResponse
    {
        $user = Auth::user();
        if (isset($user->telegramInfo->telegram_user_name)) {
            return response()->json([
                'data' => [
                    'subscribe' => true,
                    'username' => $user->telegramInfo->telegram_user_name,
                ]
            ]);
        }
        return response()->json([
            'data' => [
                'subscribe' => false,
                'username' => null,
            ]
        ]);
    }
}
