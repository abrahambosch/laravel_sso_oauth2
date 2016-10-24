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
        $user = $service->createOrGetUser(Socialite::driver($provider)->user(), $provider);

        auth()->login($user);

echo "got here" . __METHOD__ . " - " . __LINE__ . "<br>";
        dd($user);

        return redirect()->to('/home');
    }
}
