<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        if (User::query()->where(['email' => $request->email, 'service' => NULL])->first()) {
            return response()->json([
                'status' => false,
                'message' => 'На этот email занят.'
            ]);
        };

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $response = [
            'status' => true,
            'isRequiredEmailVerification' => false,
            'email' => $request->email,
            'message' => 'Вы успешно зарегистрировались'
        ];

        if($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
            $response['isRequiredEmailVerification'] = true;
            $response['message'] = 'Необходимо подтверждение mail';
        }

        return response()->json($response);
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

        if (!$user->hasVerifiedEmail()) {
            return response()->json([
                'status' => true,
                'message' => 'Необходимо подтверждение mail',
                'isRequiredEmailVerification' => true,
                'email' => $request->email
            ]);

            //abort(403, 'Email is not verified');
        }

        if ($request->has('asAdmin') && !$user->hasAnyRole(['admin', 'moderator'])) {
            return response()->json([
                'status' => false,
                'message' => 'У вас нет прав доступа'
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Пользователь успешно авторизирован',
            'isRequiredEmailVerification' => false,
            'name' => $user->name,
            'token' => $user->getBearerToken(),
            'role' => $user->role->name,
            'avatar' => isset($user->info)
                ? ($user->info->avatar ?? '/storage/icons/user_avatar.png')
                : '/storage/icons/user_avatar.png'
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->token()->revoke();
        //$request->user()->currentAccessToken()->delete();
        //auth()->user()->tokens()->delete();
        return response()->json([
            'status' => true,
            'message' => 'Вы разлогинились',
        ]);
    }



    public function emailVerify(Request $request)
    {
        $user = User::find($request->id);

        if (!$user || !hash_equals((string)$request->hash, sha1($user->email))) {
            return redirect()->route('login');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        };

        return view('auth.verify-email');;
    }


    public function resendVerificationEmail(Request $request): JsonResponse
    {

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            abort('403', 'Email not found.');
        };

        if ($user->hasVerifiedEmail()) {
            abort('403', 'Verification email not sent. Already verified.');
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => "На почту выслано подтверждение",
        ]);
    }
}
