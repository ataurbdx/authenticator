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
        'core'    => true,
        'profile' => true,
        'otp'     => env('AUTH_MODULE_OTP', true),
        '2fa'     => env('AUTH_MODULE_2FA', true),
        'pin'     => env('AUTH_MODULE_PIN', true),
        'social'  => env('AUTH_MODULE_SOCIAL', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Redirection Configuration
    |--------------------------------------------------------------------------
    | Default redirection path after successful sign-in or signup.
    |
    */
    'redirect_to' => env('AUTH_REDIRECT_TO', '/dashboard'),

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
        'web_prefix'     => '',
        'web_middleware' => ['web'],
    ],

    /*
    |--------------------------------------------------------------------------
    | UI Presentation Framework & Theme
    |--------------------------------------------------------------------------
    | Supports both Tailwind CSS (default, dark glassmorphic) and
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
        'otp_codes'        => 'otp_codes',
        'otp_channels'     => 'otp_channels',
        'user_2fa'         => 'user_2fa',
        'user_socials'     => 'user_socials',
        'social_providers' => 'social_providers',
        'settings'         => 'authenticator_settings',
    ],

    /*
    |--------------------------------------------------------------------------
    | OTP & Verification Settings
    |--------------------------------------------------------------------------
    |
    */
    'otp' => [
        'length'               => 6,
        'expiration_minutes'   => 10,
        'max_attempts'         => 5,
        'default_channel'      => 'email', // 'email' or 'phone'
        'channel_setting_name' => env('AUTH_OTP_CHANNEL_SETTING', 'authenticator'),
        'email_subject'        => env('AUTH_OTP_EMAIL_SUBJECT', 'Your Security Verification Code'),
        'debug_in_dev'         => env('APP_DEBUG', true),

        // Default Delivery Mode: 'code', 'link', or 'both'
        'default_mode'            => env('AUTH_VERIFICATION_MODE', 'both'),
        'link_expiration_minutes' => env('AUTH_VERIFICATION_LINK_EXPIRY', 60),

        // Optional Shortlink Generator hook/closure/service (null defaults to standard full URL)
        'shortener'            => null,
        'channel_modes'        => [],
        'action_tables'        => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Account Verification Policy
    |--------------------------------------------------------------------------
    | Define whether unverified users are blocked from dashboard until verified.
    | All values below can be dynamically managed from the 'authenticator_settings' table.
    |
    */
    'verification' => [
        'mandatory'                   => env('AUTH_VERIFICATION_MANDATORY', false),
        'notice_route'                => env('AUTH_VERIFICATION_NOTICE_ROUTE', 'authenticator.otp.verify'),
        'redirect_after_verification' => env('AUTH_VERIFICATION_REDIRECT', '/dashboard'),
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
