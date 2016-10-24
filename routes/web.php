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



// oauth client routes

// First route that user visits on consumer app
Route::get('/single-signon', function () {
    // Build the query parameter string to pass auth information to our request
    $query = http_build_query([
        'client_id' => 1,
        'redirect_uri' => 'http://oauthconsumer.dev/oauth/zerotouch/callback',
        'response_type' => 'code',
        'scope' => ''
    ]);

    // Redirect the user to the OAuth authorization page
    return redirect('http://local.api.zerotouch.live/oauth/authorize?' . $query);
});

// Route that user is forwarded back to after approving on server
Route::get('oauth_callback', function (Request $request) {
    $http = new GuzzleHttp\Client;

    //$vars = $request->all();
    //dd($vars);

    $response = $http->post('http://passport.dev/oauth/token', [
        'form_params' => [
            'grant_type' => 'authorization_code',
            'client_id' => 3, // from admin panel above
            'client_secret' => 'doXf9lndKwIr1hLJRFjp9UnOk5ZRTcyJlxmGi5kL', // from admin panel above
            'redirect_uri' => 'http://oauthconsumer.dev/oauth_callback',
            'code' => $request->input("code") // Get code from the callback
        ]
    ]);

    // echo the access token; normally we would save this in the DB
    //return json_decode((string) $response->getBody(), true)['access_token'];
    return json_decode((string) $response->getBody(), true);
});



// socialite routes
// todo: make this generic so provider is specified in the url
Route::get('auth/{provider}', 'Auth\SocialAuthController@redirectToProvider')->name("socialite.redirect");
Route::get('auth/{provider}/callback', 'Auth\SocialAuthController@handleProviderCallback')->name("socialite.callback");
