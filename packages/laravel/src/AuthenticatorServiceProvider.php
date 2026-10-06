<?php

namespace Ataurbdx\Authenticator;

use Illuminate\Support\ServiceProvider;
use Ataurbdx\Authenticator\Console\InstallAuthenticatorCommand;

class AuthenticatorServiceProvider extends ServiceProvider
{
    /**
     * Register package services in the container.
     */
    public function register(): void
    {
        // Merge package configuration
        $this->mergeConfigFrom(
            __DIR__ . '/../config/authenticator.php',
            'authenticator'
        );

        // Bind main services
        $this->app->singleton('authenticator.auth', function ($app) {
            return new \Ataurbdx\Authenticator\Modules\Authenticator\Services\AuthenticatorService();
        });
        $this->app->singleton(\Ataurbdx\Authenticator\Modules\Authenticator\Services\AuthenticatorService::class, function ($app) {
            return $app['authenticator.auth'];
        });

        $this->app->singleton('authenticator.otp', function ($app) {
            return new \Ataurbdx\Authenticator\Modules\Otp\Services\OtpService();
        });

        $this->app->singleton('authenticator.2fa', function ($app) {
            return new \Ataurbdx\Authenticator\Modules\TwoFactor\Services\TwoFactorService();
        });

        $this->app->singleton('authenticator.pin', function ($app) {
            return new \Ataurbdx\Authenticator\Modules\Pin\Services\PinService();
        });

        $this->app->singleton('authenticator.social', function ($app) {
            return new \Ataurbdx\Authenticator\Modules\Socialite\Services\SocialiteService();
        });

        // Bind central manager
        $this->app->singleton('authenticator', function ($app) {
            return new \Ataurbdx\Authenticator\AuthenticatorManager();
        });
    }

    /**
     * Bootstrap package services.
     */
    public function boot(): void
    {
        // 1. Load Views with Dual Framework Support (Tailwind / Bootstrap)
        $framework = config('authenticator.ui.framework', 'tailwind');
        $frameworkViewPath = __DIR__ . '/../resources/views/authenticator/' . $framework;
        if (is_dir($frameworkViewPath)) {
            $this->loadViewsFrom($frameworkViewPath, 'authenticator');
        }
        $this->loadViewsFrom(__DIR__ . '/../resources/views/authenticator', 'authenticator');

        // 2. Load Routes
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        // 3. Register Middleware Aliases
        $router = $this->app['router'];
        $router->aliasMiddleware('authenticator.active', \Ataurbdx\Authenticator\Http\Middleware\Authenticator\EnsureActiveUser::class);
        $router->aliasMiddleware('authenticator.pin', \Ataurbdx\Authenticator\Http\Middleware\Authenticator\EnsurePinUnlocked::class);
        $router->aliasMiddleware('authenticator.2fa', \Ataurbdx\Authenticator\Http\Middleware\Authenticator\RequireTwoFactor::class);
        $router->aliasMiddleware('authenticator.verified', \Ataurbdx\Authenticator\Http\Middleware\Authenticator\EnsureAuthenticatorVerified::class);

        // 4. Register Publishable Assets (Console)
        if ($this->app->runningInConsole()) {
            // Config
            $this->publishes([
                __DIR__ . '/../config/authenticator.php' => config_path('authenticator.php'),
            ], 'authenticator-config');

            // Views (Active Framework published directly to resources/views/authenticator - No vendor folder)
            $this->publishes([
                (is_dir($frameworkViewPath) ? $frameworkViewPath : __DIR__ . '/../resources/views/authenticator/tailwind') => resource_path('views/authenticator'),
            ], 'authenticator-views');

            // Views (Tailwind Specific directly to resources/views/authenticator)
            $this->publishes([
                __DIR__ . '/../resources/views/authenticator/tailwind' => resource_path('views/authenticator'),
            ], 'authenticator-views-tailwind');

            // Views (Bootstrap Specific directly to resources/views/authenticator)
            $this->publishes([
                __DIR__ . '/../resources/views/authenticator/bootstrap' => resource_path('views/authenticator'),
            ], 'authenticator-views-bootstrap');

            // Assets (JS bridge)
            $this->publishes([
                __DIR__ . '/../resources/js' => public_path('vendor/authenticator/js'),
            ], 'authenticator-assets');

            // Models (App\Models\Authenticator)
            $this->publishes([
                __DIR__ . '/Modules/Authenticator/Models' => app_path('Models/Authenticator'),
            ], 'authenticator-models');

            // Controllers (App\Http\Controllers\Authenticator)
            $this->publishes([
                __DIR__ . '/Http/Controllers/Web/Authenticator' => app_path('Http/Controllers/Authenticator/Web'),
                __DIR__ . '/Http/Controllers/Api/Authenticator' => app_path('Http/Controllers/Authenticator/Api'),
            ], 'authenticator-controllers');

            // Middleware (App\Http\Middleware\Authenticator)
            $this->publishes([
                __DIR__ . '/Http/Middleware/Authenticator' => app_path('Http/Middleware/Authenticator'),
            ], 'authenticator-middleware');

            // Granular Migration Publishing by Module (Translator style):
            // 1. Core Auth Migrations
            $this->publishes([
                __DIR__ . '/Modules/Authenticator/Migrations/2026_01_01_000001_create_users_table.php' => database_path('migrations/2026_01_01_000001_create_users_table.php'),
                __DIR__ . '/Modules/Authenticator/Migrations/2026_01_01_000002_create_user_data_table.php' => database_path('migrations/2026_01_01_000002_create_user_data_table.php'),
                __DIR__ . '/Modules/Authenticator/Migrations/2026_01_01_000009_create_authenticator_settings_table.php' => database_path('migrations/2026_01_01_000009_create_authenticator_settings_table.php'),
            ], 'migrations-core');

            // 2. PIN Migrations
            $this->publishes([
                __DIR__ . '/Modules/Pin/Migrations/2026_01_01_000003_create_user_pins_table.php' => database_path('migrations/2026_01_01_000003_create_user_pins_table.php'),
            ], 'migrations-pin');

            // 3. OTP Migrations
            $this->publishes([
                __DIR__ . '/Modules/Otp/Migrations/2026_01_01_000004_create_otp_codes_table.php' => database_path('migrations/2026_01_01_000004_create_otp_codes_table.php'),
                __DIR__ . '/Modules/Otp/Migrations/2026_01_01_000008_create_otp_channels_table.php' => database_path('migrations/2026_01_01_000008_create_otp_channels_table.php'),
            ], 'migrations-otp');

            // 4. TwoFactor (2FA) Migrations
            $this->publishes([
                __DIR__ . '/Modules/TwoFactor/Migrations/2026_01_01_000005_create_user_2fa_table.php' => database_path('migrations/2026_01_01_000005_create_user_2fa_table.php'),
            ], 'migrations-2fa');

            // 5. Socialite Migrations
            $this->publishes([
                __DIR__ . '/Modules/Socialite/Migrations/2026_01_01_000006_create_social_providers_table.php' => database_path('migrations/2026_01_01_000006_create_social_providers_table.php'),
                __DIR__ . '/Modules/Socialite/Migrations/2026_01_01_000007_create_user_socials_table.php' => database_path('migrations/2026_01_01_000007_create_user_socials_table.php'),
            ], 'migrations-social');

            // All migrations combined
            $this->publishes([
                __DIR__ . '/Modules/Authenticator/Migrations/2026_01_01_000001_create_users_table.php' => database_path('migrations/2026_01_01_000001_create_users_table.php'),
                __DIR__ . '/Modules/Authenticator/Migrations/2026_01_01_000002_create_user_data_table.php' => database_path('migrations/2026_01_01_000002_create_user_data_table.php'),
                __DIR__ . '/Modules/Pin/Migrations/2026_01_01_000003_create_user_pins_table.php' => database_path('migrations/2026_01_01_000003_create_user_pins_table.php'),
                __DIR__ . '/Modules/Otp/Migrations/2026_01_01_000004_create_otp_codes_table.php' => database_path('migrations/2026_01_01_000004_create_otp_codes_table.php'),
                __DIR__ . '/Modules/Otp/Migrations/2026_01_01_000008_create_otp_channels_table.php' => database_path('migrations/2026_01_01_000008_create_otp_channels_table.php'),
                __DIR__ . '/Modules/TwoFactor/Migrations/2026_01_01_000005_create_user_2fa_table.php' => database_path('migrations/2026_01_01_000005_create_user_2fa_table.php'),
                __DIR__ . '/Modules/Socialite/Migrations/2026_01_01_000006_create_social_providers_table.php' => database_path('migrations/2026_01_01_000006_create_social_providers_table.php'),
                __DIR__ . '/Modules/Socialite/Migrations/2026_01_01_000007_create_user_socials_table.php' => database_path('migrations/2026_01_01_000007_create_user_socials_table.php'),
                __DIR__ . '/Modules/Authenticator/Migrations/2026_01_01_000009_create_authenticator_settings_table.php' => database_path('migrations/2026_01_01_000009_create_authenticator_settings_table.php'),
            ], 'authenticator-migrations');

            // Register artisan commands
            $this->commands([
                InstallAuthenticatorCommand::class,
            ]);
        }
    }
}
