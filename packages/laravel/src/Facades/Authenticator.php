<?php

namespace Ataurbdx\Authenticator\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Ataurbdx\Authenticator\Modules\Auth\Services\AuthService auth()
 * @method static \Ataurbdx\Authenticator\Modules\Otp\Services\OtpService otp()
 * @method static \Ataurbdx\Authenticator\Modules\TwoFactor\Services\TwoFactorService twoFa()
 * @method static \Ataurbdx\Authenticator\Modules\Pin\Services\PinService pin()
 * @method static \Ataurbdx\Authenticator\Modules\Socialite\Services\SocialiteService social()
 */
class Authenticator extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'authenticator';
    }
}
