<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Socialite;
use App\SocialAccountService;

class SocialAuthController extends Controller
{
    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return Response
     */
    public function redirectToProvider($provider='github')
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return Response
     */
    public function handleProviderCallback(SocialAccountService $service, $provider='github')
    {
        //$socialiteUser = Socialite::driver($provider)->user();    // doesn't work for some reason!!
        $socialiteUser = Socialite::with($provider)->user();
        $user = $service->createOrGetUser($socialiteUser, $provider);
        auth()->login($user);
        return redirect()->to('/home');
    }
}
