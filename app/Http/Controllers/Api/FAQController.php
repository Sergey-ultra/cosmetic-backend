<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class FAQController extends Controller
{
    public function menu(): JsonResponse
    {
        $result = [
            [
                'title' => trans('faq.menu.review.title'),
                'code' => 'review',
                'list' => trans('faq.menu.review.list'),
            ],
            [
                'title' => trans('faq.menu.ref.title'),
                'code' => 'ref',
                'list' => trans('faq.menu.ref.list'),
            ],
            [
                'title' => trans('faq.menu.finance.title'),
                'code' => 'finance',
                'list' => trans('faq.menu.finance.list'),
            ]
        ];
        return response()->json(['data' => $result]);
    }
}
