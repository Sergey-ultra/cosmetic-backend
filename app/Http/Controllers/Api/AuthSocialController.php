<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class AuthSocialController extends Controller
{
    public const AVAILABLE_SERVICES = ['google', 'vk'];

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
    public function callback(string $service)
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
