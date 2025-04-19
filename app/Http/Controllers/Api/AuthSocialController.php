<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Parser\Token;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Laravel\Socialite\Facades\Socialite;

class AuthSocialController extends Controller
{
    public const AVAILABLE_SERVICES = ['google', 'vkontakte', 'facebook', 'instagram', 'mailru'];

    /**
     * @param string $service
     * @return JsonResponse
     */
    public function redirect(string $service): JsonResponse
    {
        //return Socialite::driver($service)->redirect();
        return response()->json(['url' => Socialite::with($service)->stateless()->redirect()->getTargetUrl()]);
    }

    /**
     * @param string $service
     * @return Application|RedirectResponse|Redirector
     */
    public function callback(string $service): Application|RedirectResponse|Redirector
    {
        //Socialite::driver($service)
        $user = Socialite::with($service)->stateless()->user();
        $avatar = $user->avatar;

        $existedServiceUser = User::query()
            ->where([
                'email' => $user->email,
                'service_user_id' => $user->id,
                'service' => $service
            ])
            ->first();

        if (!$existedServiceUser) {
            $existedServiceUser = User::query()
                ->create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'service' => $service,
                    'service_user_id' => $user->id,
                    'password' => encrypt('user'),
                    'ref' => Token::getToken(12),
                ]);

            $existedServiceUser->info()->create(['avatar' => $avatar]);
        } else {
            $existedServiceUser->info()->update(['avatar' => $avatar]);
        }

        $token = $existedServiceUser->getBearerToken();
        $name = $existedServiceUser->name;


        return redirect(env('APP_URL') . "/social-callback?token=$token&user_name=$name&avatar=$avatar");
    }


}
