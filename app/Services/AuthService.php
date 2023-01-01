<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService
{
    /**
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function createUser(Request $request)
    {
        return User::query()->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
    }

    /**
     * @param Request $request
     * @return User|\Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function authUser(Request $request)
    {
        if(!Auth::attempt($request->only('email', 'password'))){
            abort(401, 'These credentials do not match our records.');
        }
        return Auth::user();
    }

    /**
     * @param User $user
     */
    public function checkUserVerifiedEmail(User $user)
    {
        if (env('APP_ENV') === 'production' && !$user->hasVerifiedEmail()) {
            abort(403, 'Email is not verified');
        }
    }

    /**
     * @param User $user
     */
    public function checkUserTokens(User $user)
    {
        if ($user->tokens()) {
            $user->tokens()->delete();
        }
    }

    /**
     * @param User $user
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function controlWebHashLink(User $user, Request $request)
    {
        if (!$user || !hash_equals((string)$request->hash, sha1($user->email))) {
            return redirect()->route('login');
        }
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function controlWebUserVerifiedEmail(User $user)
    {
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login');
        }
    }

    /**
     * @param User $user
     */
    public function resendEmailCheckIssetUser($user)
    {
        if (!$user) {
            abort('403', 'Email not found.');
        }
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function resendEmailCheckUserVerifiedEmail(User $user)
    {
        if ($user->hasVerifiedEmail()) {
            abort('403', 'Verification email not sent. Already verified.');
        }
    }
}
