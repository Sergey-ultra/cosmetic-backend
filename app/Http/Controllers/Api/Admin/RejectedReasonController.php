<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\RejectedReason;
use Illuminate\Http\JsonResponse;

class RejectedReasonController extends Controller
{
    public function index(): JsonResponse
    {
        $result = RejectedReason::query()->select('id', 'reason')->get();

        return response()->json(['data' => $result]);
    }
}
