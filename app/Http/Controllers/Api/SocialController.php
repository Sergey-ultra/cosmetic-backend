<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialController extends Controller
{
    public function redirect($service)
    {
        //return Socialite::driver($service)->redirect();
        return response()->json(['url' => Socialite::with($service)->stateless()->redirect()->getTargetUrl()]);
    }

    public function callback($service)
    {
        //Socialite::driver($service)
        $user = Socialite::with($service)->stateless()->user();
        $avatar = $user->avatar;

        $existedServiceUser = User::where([
            'email' => $user->email,
            'service_user_id' => $user->id,
            'service' => $service
        ])->first();

        if (!$existedServiceUser) {
            $existedServiceUser = User::create([
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

        $token = $existedServiceUser->createToken('authToken')->plainTextToken;
        $name = $existedServiceUser->name;


        return redirect(env('APP_URL') . "/social-callback?token=$token&user_name=$name&avatar=$avatar");
    }


}
