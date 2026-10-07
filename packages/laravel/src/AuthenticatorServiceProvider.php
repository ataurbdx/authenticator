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
            return new \Ataurbdx\Authenticator\Services\AuthenticatorService();
        });
        $this->app->singleton(\Ataurbdx\Authenticator\Services\AuthenticatorService::class, function ($app) {
            return $app['authenticator.auth'];
        });
        $this->app->singleton(\Ataurbdx\Authenticator\Modules\Authenticator\Services\AuthenticatorService::class, function ($app) {
            return new \Ataurbdx\Authenticator\Modules\Authenticator\Services\AuthenticatorService();
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

        // Register package views as fallback locations so view('authenticator.sign-in') works immediately even if un-published
        if ($this->app->bound('view')) {
            if (is_dir($frameworkViewPath)) {
                $this->app['view']->addLocation($frameworkViewPath);
            }
            $this->app['view']->addLocation(__DIR__ . '/../resources/views');
        }

        // 2. Load Routes
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        if (!file_exists(base_path('routes/authenticator/routes.php')) && !file_exists(base_path('routes/authenticator/core.php'))) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        }

        // 3. Register Middleware Aliases
        $router = $this->app['router'];
        $router->aliasMiddleware('authenticator.active', \Ataurbdx\Authenticator\Http\Middleware\Authenticator\EnsureActiveUser::class);
        $router->aliasMiddleware('authenticator.pin', \Ataurbdx\Authenticator\Http\Middleware\Authenticator\EnsurePinUnlocked::class);
        $router->aliasMiddleware('authenticator.2fa', \Ataurbdx\Authenticator\Http\Middleware\Authenticator\RequireTwoFactor::class);
        $router->aliasMiddleware('authenticator.verified', \Ataurbdx\Authenticator\Http\Middleware\Authenticator\EnsureAuthenticatorVerified::class);

        // 4. Register Publishable Assets (Console)
        if ($this->app->runningInConsole()) {

            // ==========================================
            // Config & Assets & Service
            // ==========================================
            $this->publishes([
                __DIR__ . '/../config/authenticator.php' => config_path('authenticator.php'),
            ], 'authenticator-config');

            $this->publishes([
                __DIR__ . '/../resources/js/authenticator.js' => public_path('vendor/authenticator/js/authenticator.js'),
            ], 'authenticator-assets');

            $this->publishes([
                __DIR__ . '/Services/AuthenticatorService.php' => app_path('Services/AuthenticatorService.php'),
            ], 'authenticator-service');

            // ==========================================
            // MODULE 1: CORE AUTH
            // ==========================================
            // Core Migrations
            $coreMigrations = [
                __DIR__ . '/Modules/Authenticator/Migrations/2026_01_01_000001_create_users_table.php' => database_path('migrations/2026_01_01_000001_create_users_table.php'),
                __DIR__ . '/Modules/Authenticator/Migrations/2026_01_01_000002_create_user_data_table.php' => database_path('migrations/2026_01_01_000002_create_user_data_table.php'),
                __DIR__ . '/Modules/Authenticator/Migrations/2026_01_01_000009_create_authenticator_settings_table.php' => database_path('migrations/2026_01_01_000009_create_authenticator_settings_table.php'),
            ];
            $this->publishes($coreMigrations, 'migrations-core');

            // Core Controllers
            $coreControllers = [
                __DIR__ . '/../stubs/controllers/core/AccountController.php' => app_path('Http/Controllers/Authenticator/AccountController.php'),
                __DIR__ . '/../stubs/controllers/core/SigninController.php' => app_path('Http/Controllers/Authenticator/SigninController.php'),
                __DIR__ . '/../stubs/controllers/core/SignupController.php' => app_path('Http/Controllers/Authenticator/SignupController.php'),
                __DIR__ . '/../stubs/controllers/core/ForgotPasswordController.php' => app_path('Http/Controllers/Authenticator/ForgotPasswordController.php'),
                __DIR__ . '/../stubs/controllers/core/ResetPasswordController.php' => app_path('Http/Controllers/Authenticator/ResetPasswordController.php'),
            ];
            $this->publishes($coreControllers, 'authenticator-core-controllers');

            // Core Views
            $coreViews = [
                __DIR__ . '/../resources/views/authenticator/tailwind/layout.blade.php' => resource_path('views/authenticator/layout.blade.php'),
                __DIR__ . '/../resources/views/authenticator/tailwind/auth-modal.blade.php' => resource_path('views/authenticator/auth-modal.blade.php'),
                __DIR__ . '/../resources/views/authenticator/tailwind/account.blade.php' => resource_path('views/authenticator/account.blade.php'),
                __DIR__ . '/../resources/views/authenticator/tailwind/sign-in.blade.php' => resource_path('views/authenticator/sign-in.blade.php'),
                __DIR__ . '/../resources/views/authenticator/tailwind/sign-up.blade.php' => resource_path('views/authenticator/sign-up.blade.php'),
                __DIR__ . '/../resources/views/authenticator/tailwind/nav-tabs.blade.php' => resource_path('views/authenticator/nav-tabs.blade.php'),
                __DIR__ . '/../resources/views/authenticator/tailwind/social-btn.blade.php' => resource_path('views/authenticator/social-btn.blade.php'),
                __DIR__ . '/../resources/views/authenticator/tailwind/social-callback.blade.php' => resource_path('views/authenticator/social-callback.blade.php'),
                __DIR__ . '/../resources/views/authenticator/tailwind/forgot-password.blade.php' => resource_path('views/authenticator/forgot-password.blade.php'),
                __DIR__ . '/../resources/views/authenticator/tailwind/reset-password.blade.php' => resource_path('views/authenticator/reset-password.blade.php'),
                __DIR__ . '/../resources/views/authenticator/tailwind/forms' => resource_path('views/authenticator/forms'),
                __DIR__ . '/../resources/views/authenticator/tailwind/partials' => resource_path('views/authenticator/partials'),
            ];
            $this->publishes($coreViews, 'authenticator-core-views');

            // Core Routes
            $coreRoutes = [
                __DIR__ . '/../routes/core.php' => base_path('routes/authenticator/core.php'),
            ];
            $this->publishes($coreRoutes, 'authenticator-core-routes');

            // Core Full Module Aggregate
            $this->publishes(array_merge(
                $coreMigrations,
                $coreControllers,
                $coreViews,
                $coreRoutes,
                [
                    __DIR__ . '/../config/authenticator.php' => config_path('authenticator.php'),
                    __DIR__ . '/../resources/js/authenticator.js' => public_path('vendor/authenticator/js/authenticator.js'),
                    __DIR__ . '/Services/AuthenticatorService.php' => app_path('Services/AuthenticatorService.php'),
                ]
            ), 'authenticator-core');

            // ==========================================
            // MODULE 2: PROFILE & PORTAL
            // ==========================================
            // Profile Controllers
            $profileControllers = [
                __DIR__ . '/../stubs/controllers/profile/AuthenticatorController.php' => app_path('Http/Controllers/Authenticator/AuthenticatorController.php'),
            ];
            $this->publishes($profileControllers, 'authenticator-profile-controllers');

            // Profile Views
            $profileViews = [
                __DIR__ . '/../resources/views/authenticator/tailwind/profile' => resource_path('views/authenticator/profile'),
            ];
            $this->publishes($profileViews, 'authenticator-profile-views');

            // Profile Routes
            $profileRoutes = [
                __DIR__ . '/../routes/profile.php' => base_path('routes/authenticator/profile.php'),
            ];
            $this->publishes($profileRoutes, 'authenticator-profile-routes');

            // Profile Full Module Aggregate
            $this->publishes(array_merge(
                $profileControllers,
                $profileViews,
                $profileRoutes
            ), 'authenticator-profile');

            // ==========================================
            // MODULE 3: OTP VERIFICATION
            // ==========================================
            // OTP Migrations
            $otpMigrations = [
                __DIR__ . '/Modules/Otp/Migrations/2026_01_01_000004_create_otp_codes_table.php' => database_path('migrations/2026_01_01_000004_create_otp_codes_table.php'),
                __DIR__ . '/Modules/Otp/Migrations/2026_01_01_000008_create_otp_channels_table.php' => database_path('migrations/2026_01_01_000008_create_otp_channels_table.php'),
            ];
            $this->publishes($otpMigrations, 'migrations-otp');

            // OTP Controllers
            $otpControllers = [
                __DIR__ . '/../stubs/controllers/otp/OtpVerificationController.php' => app_path('Http/Controllers/Authenticator/OtpVerificationController.php'),
            ];
            $this->publishes($otpControllers, 'authenticator-otp-controllers');

            // OTP Views
            $otpViews = [
                __DIR__ . '/../resources/views/authenticator/tailwind/verify.blade.php' => resource_path('views/authenticator/verify.blade.php'),
                __DIR__ . '/../resources/views/authenticator/tailwind/otp' => resource_path('views/authenticator/otp'),
            ];
            $this->publishes($otpViews, 'authenticator-otp-views');

            // OTP Routes
            $otpRoutes = [
                __DIR__ . '/../routes/otp.php' => base_path('routes/authenticator/otp.php'),
            ];
            $this->publishes($otpRoutes, 'authenticator-otp-routes');

            // OTP Full Module Aggregate
            $this->publishes(array_merge(
                $otpMigrations,
                $otpControllers,
                $otpViews,
                $otpRoutes
            ), 'authenticator-otp');

            // ==========================================
            // MODULE 4: TWO-FACTOR (2FA)
            // ==========================================
            $twoFactorMigrations = [
                __DIR__ . '/Modules/TwoFactor/Migrations/2026_01_01_000005_create_user_2fa_table.php' => database_path('migrations/2026_01_01_000005_create_user_2fa_table.php'),
            ];
            $this->publishes($twoFactorMigrations, 'migrations-2fa');

            $twoFactorControllers = [
                __DIR__ . '/../stubs/controllers/2fa/TwoFactorController.php' => app_path('Http/Controllers/Authenticator/TwoFactorController.php'),
            ];
            $this->publishes($twoFactorControllers, 'authenticator-2fa-controllers');

            $twoFactorViews = [
                __DIR__ . '/../resources/views/authenticator/tailwind/two-factor' => resource_path('views/authenticator/two-factor'),
            ];
            $this->publishes($twoFactorViews, 'authenticator-2fa-views');

            $twoFactorRoutes = [
                __DIR__ . '/../routes/2fa.php' => base_path('routes/authenticator/2fa.php'),
            ];
            $this->publishes($twoFactorRoutes, 'authenticator-2fa-routes');

            $this->publishes(array_merge(
                $twoFactorMigrations,
                $twoFactorControllers,
                $twoFactorViews,
                $twoFactorRoutes
            ), 'authenticator-2fa');

            // ==========================================
            // MODULE 5: PIN CONTENT LOCK
            // ==========================================
            $pinMigrations = [
                __DIR__ . '/Modules/Pin/Migrations/2026_01_01_000003_create_user_pins_table.php' => database_path('migrations/2026_01_01_000003_create_user_pins_table.php'),
            ];
            $this->publishes($pinMigrations, 'migrations-pin');

            $pinControllers = [
                __DIR__ . '/../stubs/controllers/pin/PinController.php' => app_path('Http/Controllers/Authenticator/PinController.php'),
            ];
            $this->publishes($pinControllers, 'authenticator-pin-controllers');

            $pinViews = [
                __DIR__ . '/../resources/views/authenticator/tailwind/pin' => resource_path('views/authenticator/pin'),
            ];
            $this->publishes($pinViews, 'authenticator-pin-views');

            $pinRoutes = [
                __DIR__ . '/../routes/pin.php' => base_path('routes/authenticator/pin.php'),
            ];
            $this->publishes($pinRoutes, 'authenticator-pin-routes');

            $this->publishes(array_merge(
                $pinMigrations,
                $pinControllers,
                $pinViews,
                $pinRoutes
            ), 'authenticator-pin');

            // ==========================================
            // MODULE 6: SOCIALITE
            // ==========================================
            $socialMigrations = [
                __DIR__ . '/Modules/Socialite/Migrations/2026_01_01_000006_create_social_providers_table.php' => database_path('migrations/2026_01_01_000006_create_social_providers_table.php'),
                __DIR__ . '/Modules/Socialite/Migrations/2026_01_01_000007_create_user_socials_table.php' => database_path('migrations/2026_01_01_000007_create_user_socials_table.php'),
            ];
            $this->publishes($socialMigrations, 'migrations-social');

            $socialControllers = [
                __DIR__ . '/../stubs/controllers/social/SocialiteController.php' => app_path('Http/Controllers/Authenticator/SocialiteController.php'),
            ];
            $this->publishes($socialControllers, 'authenticator-social-controllers');

            $socialViews = [
                __DIR__ . '/../resources/views/authenticator/tailwind/social-btn.blade.php' => resource_path('views/authenticator/social-btn.blade.php'),
                __DIR__ . '/../resources/views/authenticator/tailwind/social-callback.blade.php' => resource_path('views/authenticator/social-callback.blade.php'),
                __DIR__ . '/../resources/views/authenticator/tailwind/socialite' => resource_path('views/authenticator/socialite'),
            ];
            $this->publishes($socialViews, 'authenticator-social-views');

            $socialRoutes = [
                __DIR__ . '/../routes/social.php' => base_path('routes/authenticator/social.php'),
            ];
            $this->publishes($socialRoutes, 'authenticator-social-routes');

            $this->publishes(array_merge(
                $socialMigrations,
                $socialControllers,
                $socialViews,
                $socialRoutes
            ), 'authenticator-social');

            // ==========================================
            // GLOBAL AGGREGATE TAGS
            // ==========================================
            $allControllers = array_merge($coreControllers, $profileControllers, $otpControllers, $twoFactorControllers, $pinControllers, $socialControllers);
            $this->publishes($allControllers, 'authenticator-controllers');

            $allViews = array_merge(
                [
                    __DIR__ . '/../resources/views/authenticator/tailwind' => resource_path('views/authenticator'),
                ],
                $coreViews,
                $profileViews,
                $otpViews,
                $twoFactorViews,
                $pinViews,
                $socialViews
            );
            $this->publishes($allViews, 'authenticator-views');

            $allRoutes = array_merge($coreRoutes, $profileRoutes, $otpRoutes, $twoFactorRoutes, $pinRoutes, $socialRoutes);
            $this->publishes($allRoutes, 'authenticator-routes');

            $allMigrations = array_merge($coreMigrations, $otpMigrations, $twoFactorMigrations, $pinMigrations, $socialMigrations);
            $this->publishes($allMigrations, 'authenticator-migrations');

            // Master Everything Tag
            $this->publishes(array_merge(
                [
                    __DIR__ . '/../config/authenticator.php' => config_path('authenticator.php'),
                    __DIR__ . '/../resources/js/authenticator.js' => public_path('vendor/authenticator/js/authenticator.js'),
                    __DIR__ . '/Services/AuthenticatorService.php' => app_path('Services/AuthenticatorService.php'),
                ],
                $allControllers,
                $allViews,
                $allRoutes,
                $allMigrations
            ), 'authenticator-all');

            // Register artisan installer command
            $this->commands([
                InstallAuthenticatorCommand::class,
            ]);
        }
    }
}
