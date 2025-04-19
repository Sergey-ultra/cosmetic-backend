<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClientMessageRequest;
use App\Models\ClientMessage;
use App\Services\EntityStatus;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ClientMessagesController extends Controller
{
    //Раздел о нас
    public function index(): JsonResponse
    {
        $result = ClientMessage::query()->where('status', EntityStatus::PUBLISHED)->get();

        return response()->json(['data' => $result]);
    }

    public function store(ClientMessageRequest $request): JsonResponse
    {
        $newMessage = ClientMessage::query()->create($request->all());
        return response()->json(['data' => $newMessage], Response::HTTP_CREATED);
    }
}
