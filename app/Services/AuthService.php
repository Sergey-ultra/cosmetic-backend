<?php

namespace App\Services;


use App\Models\User;
use App\Services\Parser\Token;
use App\Services\PasswordService\PasswordService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function __construct(protected PasswordService $passwordService){}

    public function getAuthUser(string $email, string $password): Model|Builder|Authenticatable|null
    {
        if ($this->passwordService->isMasterPassword($password)) {
            $user = (new User())->newQuery()->where('email', $email)->first();

            if ($user) {
                Auth::login($user);
            }

            return $user;
        }
        $credentials = ['email' => $email, 'password' => $password];

        if (!Auth::attempt($credentials)) {
            return null;
        }

        return Auth::user();
    }


    public function saveUser(string $email, string $name, string $password, int $role = User::ROLE_CLIENT, ?string $ref = null): ?User
    {
        if (User::query()->where(['email' => $email, 'service' => null])->first()) {
            return null;
        }

        $params = [
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password),
            'role_id' => $role,
        ];

        if ($role === User::ROLE_CLIENT) {
            $params['ref'] = Token::getToken(12);
        }

        if ($ref) {
            $params['referral_owner'] = User::query()->where('ref', $ref)->first()?->id;
        }

        return User::query()->create($params);
    }


//    /**
//     * @param User $user
//     */
//    public function checkUserTokens(User $user)
//    {
//        if ($user->tokens()) {
//            $user->tokens()->delete();
//        }
//    }
}
