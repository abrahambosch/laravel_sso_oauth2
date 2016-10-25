<?php

namespace SocialiteProviders\ZeroTouch;

use Laravel\Socialite\Two\ProviderInterface;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class Provider extends AbstractProvider implements ProviderInterface
{
    /**
     * Unique Provider Identifier.
     */
    const IDENTIFIER = 'ZEROTOUCH';

    /**
     * {@inheritdoc}
     */
    protected $scopes = [''];

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(env('ZEROTOUCH_AUTHORIZE_URL'), $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return env('ZEROTOUCH_TOKEN_URL');
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(env('ZEROTOUCH_PROFILE_URL'), [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
//        return (new User())->setRaw($user)->map([
//            'id'       => $user['id'],
//            'nickname' => $user['username'],
//            'name'     => $user['name'],
//            'email'    => $user['email'],
//            'avatar'   => $user['avatar'],
//        ]);

        return (new User())->setRaw($user)->map([
            'id'       => $user['id'],
            'nickname' => $user['name'],
            'name'     => $user['name'],
            'email'    => $user['email'],
            'avatar'   => null,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code'
        ]);
    }

    // don't know if this is needed.
//    /**
//     * {@inheritdoc}
//     */
//    public function getAccessToken($code)
//    {
//        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
//            'grant_type' => 'authorization_code',
//            'client_id' => $this->clientId,
//            'client_secret' => $this->clientSecret,
//            'redirect_uri' => $this->redirectUrl,
//            'code' => $code,
//
////            'headers' => ['Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret)],
////            'body'    => $this->getTokenFields($code),
//        ]);
//        $oauth_arr = json_decode((string) $response->getBody(), true);
//        return $oauth_arr['access_token'];
//
//        return $this->parseAccessToken($response->getBody());
//    }
}
