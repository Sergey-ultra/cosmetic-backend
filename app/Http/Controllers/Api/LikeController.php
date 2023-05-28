<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReviewLike;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function createOrUpdate(Request $request, int $id): JsonResponse
    {
        if ($request->input('entity') === 'review') {
            $status = ReviewLike::query()->updateOrCreate([
                'review_id' => $id,
                'plus_ip_address' => $request->ip()
            ], []);
           // dd($status);
        }

        return response()->json(['data' => ['status' => 'success']]);
    }
}
