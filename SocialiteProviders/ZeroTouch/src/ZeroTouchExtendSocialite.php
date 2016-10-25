<?php

namespace SocialiteProviders\ZeroTouch;

use SocialiteProviders\Manager\SocialiteWasCalled;

class ZeroTouchExtendSocialite
{
    /**
     * Execute the provider.
     */
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite('zerotouch', __NAMESPACE__.'\Provider');
    }
}
