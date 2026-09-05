<?php

namespace Ataurbdx\Authenticator;

use Ataurbdx\Authenticator\Modules\Auth\Services\AuthService;
use Ataurbdx\Authenticator\Modules\Otp\Services\OtpService;
use Ataurbdx\Authenticator\Modules\TwoFactor\Services\TwoFactorService;
use Ataurbdx\Authenticator\Modules\Pin\Services\PinService;
use Ataurbdx\Authenticator\Modules\Socialite\Services\SocialiteService;

class AuthenticatorManager
{
    /**
     * Get the Core Authentication service.
     */
    public function auth(): AuthService
    {
        return app('authenticator.auth');
    }

    /**
     * Get the OTP Verification service.
     */
    public function otp(): OtpService
    {
        return app('authenticator.otp');
    }

    /**
     * Get the Two-Factor (2FA) service.
     */
    public function twoFa(): TwoFactorService
    {
        return app('authenticator.2fa');
    }

    /**
     * Get the PIN Content Lock service.
     */
    public function pin(): PinService
    {
        return app('authenticator.pin');
    }

    /**
     * Get the Dynamic Socialite service.
     */
    public function social(): SocialiteService
    {
        return app('authenticator.social');
    }

    /**
     * Forward dynamic method calls directly to the core AuthService.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return $this->auth()->$method(...$parameters);
    }
}
