<?php

namespace App\Http\Controllers\Api\Admin;

use App\Configuration;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function getIsRequiredEmailVerification(Configuration $congService): JsonResponse
    {
        $result = $congService->getBoolean('is_required_email_verification');

        return response()->json(['data'=> $result]);
    }
    public function setIsRequiredEmailVerification(Request $request, Configuration $congService): JsonResponse
    {
        $congService->setBoolean('is_required_email_verification', $request->input('is_required_email_verification'));

        return response()->json(['data'=> 'success']);
    }
}
