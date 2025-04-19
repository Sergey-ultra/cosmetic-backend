<?php
declare(strict_types=1);


namespace App\Http\Controllers\Api\Supplier;

use App\Http\Controllers\Controller;
use App\Http\Requests\Supplier\LoginRequest;
use App\Http\Requests\Supplier\RegisterRequest;
use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        if (User::where(['email' => $request->email, 'service' => null])->first()) {
            return response()->json([
                'status' => false,
                'message' => 'На этот email уже зарегистрирован аккаунт.
                 Если это ваш email и вы регистрировались ранее, мы можем напомнить пароль'
            ]);
        };

        User::query()->create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'role_id' => User::ROLE_SUPPLIER,
        ]);

        return response()->json(['data' => [
            'status'=> true,
            'message' =>'Добавление магазина успешно создано'
            ]
        ]);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => false,
                'message' => 'Неправильный логин или пароль'
            ]);
        }

        $user = Auth::user();


        if (!$user->hasAnyRole(['supplier'])) {
            return response()->json([
                'status' => false,
                'message' => 'У вас нет прав доступа'
            ]);
        }

        $token = $user->createToken('authToken')->plainTextToken;
        return response()->json([
            'status' => true,
            'isRequiredEmailVerification' => false,
            'user_name' => $user->name,
            'token' => $token,
            'message' => 'Пользователь успешно авторизирован',
            'avatar' => isset($user->info)
                ? $user->info->avatar ??  UserInfo::DEFAULT_AVATAR
                :  UserInfo::DEFAULT_AVATAR,
            'role' => $user->role(),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        //auth()->user()->tokens()->delete();
        return response()->json([
            'status' => true,
            'message' => 'Вы разлогинились',
        ]);
    }
}
