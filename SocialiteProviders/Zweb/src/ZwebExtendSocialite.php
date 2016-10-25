<?php

namespace SocialiteProviders\Zweb;

use SocialiteProviders\Manager\SocialiteWasCalled;

class ZwebExtendSocialite
{
    /**
     * Execute the provider.
     */
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite('zweb', __NAMESPACE__.'\Provider');
    }
}
