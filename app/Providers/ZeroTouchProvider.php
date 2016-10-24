<?php

namespace App\Providers;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class ZeroTouchProvider extends AbstractProvider implements ProviderInterface {

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        //return $this->buildAuthUrlFromBase('https://accounts.spotify.com/authorize', $state);
        return $this->buildAuthUrlFromBase(env('ZEROTOUCH_AUTHORIZE_URL'), $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        //return 'https://accounts.spotify.com/api/token';
        return env('ZEROTOUCH_TOKEN_URL');
    }

//    /**
//     * {@inheritdoc}
//     */
//    public function getAccessToken($code)
//    {
//        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
//            'headers' => ['Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret)],
//            'body'    => $this->getTokenFields($code),
//        ]);
//
//        return $this->parseAccessToken($response->getBody());
//    }

//    /**
//     * {@inheritdoc}
//     */
//    protected function getTokenFields($code)
//    {
//        return array_add(
//            parent::getTokenFields($code), 'grant_type', 'authorization_code'
//        );
//    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(env('ZEROTOUCH_PROFILE_URL'), [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function formatScopes(array $scopes, $scopeSeparator)
    {
        return implode(' ', $scopes);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
//        return (new User)->setRaw($user)->map([
//            'id'       => $user['id'],
//            'nickname' => $user['nickname'],
//            'name'     => $user['name'],
//            'avatar'   => !empty($user['avatar']) ? $user['avatar']: null,
//        ]);

        return (new User)->setRaw($user)->map([
            'id'       => $user['id'],
            'nickname' => $user['name'],
            'name'     => $user['name'],
            'avatar'   => !empty($user['avatar']) ? $user['avatar']: null,
        ]);
    }

}