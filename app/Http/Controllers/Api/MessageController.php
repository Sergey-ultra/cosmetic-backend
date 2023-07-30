<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function myMessages(UserRepository $userRepository): JsonResponse
    {
        $userid = Auth::guard('api')->id();
        $result = $userRepository->getChatsByUserId($userid);

        return response()->json(['data' => $result]);
    }
}
