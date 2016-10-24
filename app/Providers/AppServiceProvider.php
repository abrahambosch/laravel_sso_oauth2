<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Providers\ZeroTouchProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $socialite = $this->app->make('Laravel\Socialite\Contracts\Factory');
        $socialite->extend(
            'zerotouch',
            function ($app) use ($socialite) {
                $config = $app['config']['services.zerotouch'];
                return $socialite->buildProvider(ZeroTouchProvider::class, $config);
            }
        );
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
