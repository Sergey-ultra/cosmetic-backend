<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    public function index(): JsonResponse
    {
        $list = Country::query()
            ->whereNotNull('name_en')
            ->where('name_en', 'not like', "%/%")
            ->get()
            ->pluck('name_en')
            ->all()
        ;
        return response()->json(['data' => $list]);
    }
}
