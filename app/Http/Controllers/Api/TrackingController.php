<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrackingRequest;
use App\Jobs\AdminNotificationJob;
use App\Models\Tracking;
use Illuminate\Http\JsonResponse;


class TrackingController extends Controller
{
    public function addToTracking(TrackingRequest $request): JsonResponse
    {
        Tracking::create($request->all());

        if (request()->ip() !== config('telegrambot.admin_ip')) {
            $message = "Пользователь с email $request->email подписался на отслеживание цены";
            AdminNotificationJob::dispatch($message);
        }

        return response()->json([
            'data'=> [
                'status' => 'success'
            ]
        ]);
    }
}
