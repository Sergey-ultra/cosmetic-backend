<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Review;
use App\Models\User;
use App\Services\EntityStatus;
use Illuminate\Http\JsonResponse;


class MainController extends Controller
{
    public function index(): JsonResponse
    {
        $userCount = User::query()->where('role_id', User::ROLE_CLIENT)->count();
        $reviewCount = Review::query()->where('status', EntityStatus::PUBLISHED)->count();
        $commentCount = Comment::query()->where('status', EntityStatus::PUBLISHED)->count();

        return response()->json(['data'=> [
            'user_count' => $userCount,
            'review_count' => $reviewCount,
            'comment_count' => $commentCount,
        ]]);
    }
}
