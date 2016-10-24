<?php

namespace App\Listeners;

use SocialiteProviders\Manager\SocialiteWasCalled;
use App\Providers\ZeroTouchProvider;

class ZeroTouchExtendSocialite
{
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite('zerotouch', ZeroTouchProvider::class);
    }
}