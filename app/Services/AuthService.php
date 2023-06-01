<?php

namespace App\Services;


use App\Models\User;
use App\Services\PasswordService\PasswordService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function __construct(protected PasswordService $passwordService){}

    /**
     * @param string $email
     * @param string $password
     * @return Model|Builder|Authenticatable|null
     */
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

    public function saveUser(string $email, string $name, string $password, int $role = User::ROLE_CLIENT): ?User
    {
        if (User::query()->where(['email' => $email, 'service' => NULL])->first()) {
            return null;
        }

        return User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password),
            'role_id' => $role,
        ]);
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


}
