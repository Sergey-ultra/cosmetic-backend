<?php

namespace App\Http\Controllers\Api;

use App\Configuration;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Models\UserInfo;
use App\Services\AuthService;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportException;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, Configuration $configuration, AuthService $authService): JsonResponse
    {
        $email = $request->input('email');

        $user = $authService->saveUser($email, $request->input('name'), $request->input('password'), User::ROLE_CLIENT, $request->input('ref'));

        if (is_null($user)) {
            return response()->json([
                'status' => false,
                'message' => 'Этот email уже используется.',
            ]);
        }

        $response = [
            'status' => true,
            'isRequiredEmailVerification' => false,
            'email' => $email,
            'message' => 'Вы успешно зарегистрировались',
        ];

        $isRequiredEmailVerification = $configuration->getBoolean('is_required_email_verification');

        if ($isRequiredEmailVerification && $user instanceof MustVerifyEmail && !$user->hasVerifiedEmail()) {
            try {
                $user->sendEmailVerificationNotification();
                $response['message'] = "На ваш email $email выслано подтверждение аккаунта";
            } catch (TransportException $e) {
                $response['message'] = 'Не удалось отправить email верификации. Попробуйте отправить еще раз';
                $response['data'] = $e->getMessage();
            }

            $response['isRequiredEmailVerification'] = true;
        }

        return response()->json($response);
    }



    public function login(LoginRequest $request, Configuration $configuration, AuthService $authService): JsonResponse
    {
        $user = $authService->getAuthUser($request->input('email'), $request->input('password'));

        if (is_null($user)) {
            return response()->json([
                'status' => false,
                'message' => 'Неправильный логин или пароль'
            ]);
        }


        $isRequiredEmailVerification = $configuration->getBoolean('is_required_email_verification');


        if (
            $isRequiredEmailVerification &&
            $user->role_id !== User::ROLE_BOT &&
            $user instanceof MustVerifyEmail && !$user->hasVerifiedEmail()
        ) {
            return response()->json([
                'status' => true,
                'message' => 'Необходимо подтверждение email',
                'isRequiredEmailVerification' => true,
                'email' => $request->input('email'),
            ]);
        }

        if ($request->has('asAdmin') && !$user->hasAnyRole(['admin', 'review_editor'])) {
            return response()->json([
                'status' => false,
                'message' => 'У вас нет прав доступа'
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Вы успешно авторизированы',
            'isRequiredEmailVerification' => false,
            'name' => $user->name,
            'token' => $user->getBearerToken(),
            'role' => $user->role(),
            'balance' => $user->balanceNormal,
            'avatar' => isset($user->info)
                ? $user->info->avatar ?? UserInfo::DEFAULT_AVATAR
                : UserInfo::DEFAULT_AVATAR
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

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = Auth::user();
        if (!Hash::check($request->input('password'), $user->password)){
            return response()->json([
                'status' => false,
                'message' => 'Неверный пароль'
            ]);
        }

        $user->update([
            'password' => bcrypt($request->input('new_password'))
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Пароль успешно изменен',
        ]);
    }

    public function emailVerify(Request $request): RedirectResponse|View|array|null
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


    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            $status = Password::sendResetLink(
                $request->only('email')
            );
        } catch (TransportException $e) {
            return response()->json([
                'message' => "Не удалось отправить новый пароль на email",
                'data' => $e->getMessage()
            ]);
        }
        return response()->json([
            'message' => "На почту выслан новый пароль",
            'data' => $status
        ]);
    }

    public function passwordReset()
    {

    }


    public function resendVerificationEmail(Request $request): JsonResponse
    {
        $user = User::query()->where('email', $request->email)->first();

        if (!$user) {
            abort('403', 'Email not found.');
        };

        if ($user->hasVerifiedEmail()) {
            abort('403', 'Verification email not sent. Already verified.');
        }

        try {
            $user->sendEmailVerificationNotification();
        } catch (TransportException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'message' => "На почту выслано подтверждение",
        ]);
    }
}
