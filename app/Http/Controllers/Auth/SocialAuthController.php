<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Socialite;
use App\SocialAccountService;
use Illuminate\Http\Request;
use App\User;
use App\SocialAccount;

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


    public function signinFacebookUser(Request $request)
    {
        $signed_request = $request->input('signed_request');
        if (empty($signed_request)) return response()->json(['status' => 0, 'error' => 'signed_request was missing. '], 400);
        $decoded_data = $this->facebook_parse_signed_request($signed_request);
        if (empty($decoded_data)) return response()->json(['status' => 0, 'error' => 'Bad Signed JSON Signature!'], 400);

        $facebook_user_id = $decoded_data['user_id'];

        $socialAccount = SocialAccount::where(['provider_user_id' => $facebook_user_id, 'provider' => 'facebook'])->first();
        if (empty($socialAccount)) {
            return response()->json(['status' => 0, 'error' => 'Social Id not found. '], 400);
        }

        $user = $socialAccount->user;

        auth()->login($user);

        return response()->json(['status' => 1, 'message' => 'You are logged in. ']);
    }

    public function facebookDecodeSignedRequest(Request $request)
    {
        $signed_request = $request->input('signed_request');
        if (empty($signed_request)) return response()->json(['error' => 'signed_request was missing. ']);
        $decoded_data = $this->facebook_parse_signed_request($signed_request);
        if (empty($decoded_data)) return response()->json(['error' => 'Bad Signed JSON Signature!']);

        return response()->json($decoded_data);
    }



    protected function facebook_parse_signed_request($signed_request) {
        list($encoded_sig, $payload) = explode('.', $signed_request, 2);

        $secret = config("services.facebook.client_secret"); // Use your app secret here

        // decode the data
        $sig = $this->base64_url_decode($encoded_sig);
        $data = json_decode($this->base64_url_decode($payload), true);

        // confirm the signature
        $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
        if ($sig !== $expected_sig) {
            //error_log('Bad Signed JSON signature!');
            return null;
        }

        return $data;
    }

    protected function base64_url_decode($input) {
        return base64_decode(strtr($input, '-_', '+/'));
    }

}
