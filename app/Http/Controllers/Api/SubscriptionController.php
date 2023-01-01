<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubscriptionRequest;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;


class SubscriptionController extends Controller
{
   public function store(SubscriptionRequest $request): JsonResponse
   {
       Subscription::updateOrCreate($request->all());

       return response()->json([
           'status'=> true,
           'message' =>'Подписка успешно создана'
       ]);
   }
}
