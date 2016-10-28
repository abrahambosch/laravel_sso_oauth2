<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

use Illuminate\Http\Request;

use App\SocialAccount;


Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => ['auth']], function () {
    Route::get('/passport', function () {
        return view('passport');
    });
});


Auth::routes();

Route::get('/home', 'HomeController@index');

// socialite routes
Route::get('auth/{provider}', 'Auth\SocialAuthController@redirectToProvider')->name("socialite.redirect");
Route::get('auth/{provider}/callback', 'Auth\SocialAuthController@handleProviderCallback')->name("socialite.callback");
Route::get('auth/facebook/decodeSignedRequest', 'Auth\SocialAuthController@facebookDecodeSignedRequest');
Route::get('auth/facebook/signinFacebookUser', 'Auth\SocialAuthController@signinFacebookUser');



Route::get('/getSocialAccounts', function (Request $request) {
    //$user = $request->user();
    $provider_user_id = $request->input('provider_user_id');
    //$user = Auth::user();
    $user = null;

    //$socialAccounts = $user->socialAccounts()->get();
    $socialAccounts = SocialAccount::where(['provider_user_id' => $provider_user_id, 'provider' => 'facebook'])->first();

    //$socialAccounts = $user->socialAccounts()->where(['provider_user_id' => $provider_user_id, 'provider' => 'facebook'])->first();
    if ($socialAccounts) {
        $user = $socialAccounts->user;
    }

    return Response::json(compact('socialAccounts', 'user'));
})->middleware('auth');


/**
 * example of how to get a token for an authenticated user.
 */
Route::get('/jwt', function (Request $request) {
    //$user = $request->user();
    $user = Auth::user();

    $token = JWTAuth::fromUser($user);

    return Response::json(compact('token'));
})->middleware('auth');


/**
 * test endpoint that is restricted by JWT.
 */
Route::get('/restricted', [
    'before' => 'jwt-auth',
    function () {
        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);

        return Response::json([
            'data' => [
                'email' => $user->email,
                'registered_at' => $user->created_at->toDateTimeString()
            ]
        ]);
    }
]);






