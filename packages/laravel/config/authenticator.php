<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Active Modules (On-Demand & Pluggable)
    |--------------------------------------------------------------------------
    | Consistent with the Translator ecosystem. Enable or disable any module
    | on-demand. Only enabled modules will register their migrations, routes,
    | middleware, and active runtime handlers.
    |
    */
    'modules' => [
        'core'   => true,
        'otp'    => env('AUTH_MODULE_OTP', true),
        '2fa'    => env('AUTH_MODULE_2FA', true),
        'pin'    => env('AUTH_MODULE_PIN', true),
        'social' => env('AUTH_MODULE_SOCIAL', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Routing Configuration
    |--------------------------------------------------------------------------
    | Define prefixes and middleware for API and Web view routes.
    |
    */
    'routes' => [
        'api_prefix'     => 'api/v1/auth',
        'api_middleware' => ['api'],
        'web_prefix'     => 'auth',
        'web_middleware' => ['web'],
    ],

    /*
    |--------------------------------------------------------------------------
    | UI Presentation Framework & Theme
    |--------------------------------------------------------------------------
    | Supports both Tailwind CSS (default, modeled after asset-sheba) and
    | Bootstrap 5. Change 'framework' to 'bootstrap' or 'tailwind'.
    |
    */
    'ui' => [
        'framework' => env('AUTH_UI_FRAMEWORK', 'tailwind'), // 'tailwind' or 'bootstrap'
        'theme'     => env('AUTH_UI_THEME', 'dark'),        // 'dark' or 'light'
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Table Names
    |--------------------------------------------------------------------------
    | Standardized table names adhering to the user_* unified namespace.
    |
    */
    'tables' => [
        'users'            => 'users',
        'user_data'        => 'user_data',
        'user_pins'        => 'user_pins',
        'user_otps'        => 'user_otps',
        'user_2fa'         => 'user_2fa',
        'user_socials'     => 'user_socials',
        'social_providers' => 'social_providers',
    ],

    /*
    |--------------------------------------------------------------------------
    | OTP Module Settings
    |--------------------------------------------------------------------------
    |
    */
    'otp' => [
        'length'             => 6,
        'expiration_minutes' => 10,
        'max_attempts'       => 5,
        'default_channel'    => 'email', // email or phone
        'debug_in_dev'       => env('APP_DEBUG', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Two-Factor Authentication (2FA) Module Settings
    |--------------------------------------------------------------------------
    |
    */
    'two_factor' => [
        'issuer'               => env('APP_NAME', 'Authenticator'),
        'recovery_codes_count' => 8,
    ],

    /*
    |--------------------------------------------------------------------------
    | PIN Content Lock Module Settings
    |--------------------------------------------------------------------------
    |
    */
    'pin' => [
        'default_timeout_minutes' => 15,
        'max_attempts'            => 5,
        'lockout_minutes'         => 15,
    ],

    /*
    |--------------------------------------------------------------------------
    | Social Accounts Module Settings
    |--------------------------------------------------------------------------
    |
    */
    'social' => [
        'auto_link_existing_email'     => true,
        'create_temp_email_if_missing' => true,
    ],

];
