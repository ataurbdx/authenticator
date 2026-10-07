<?php

namespace Ataurbdx\Authenticator\Console;

use Illuminate\Console\Command;

class InstallAuthenticatorCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'authenticator:install
                            {--type= : Specific module to install (core, profile, otp, 2fa, pin, social, all)}
                            {--framework= : CSS Framework for Auth views (tailwind, bootstrap, all)}
                            {--all : Install all modules without interactive prompt}
                            {--core : Include Core Auth module}
                            {--profile : Include Profile & Dashboard Portal module}
                            {--otp : Include OTP Verification module}
                            {--2fa : Include Two-Factor Authentication module}
                            {--pin : Include PIN Content Lock module}
                            {--social : Include Social Accounts module}';

    /**
     * The console command description.
     */
    protected $description = 'Install and configure Authenticator modules on-demand with ready-made Controllers, Blade Views, Routes, and Migrations';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('====================================================');
        $this->info('  🚀  AUTHENTICATOR — MODULAR SUITE INSTALLER       ');
        $this->info('====================================================');

        // Determine which modules to install
        $selectedModules = $this->determineModules();

        // Determine UI Framework
        $framework = $this->determineFramework();

        $this->line('');
        $this->info('Selected Modules for Installation:');
        foreach ($selectedModules as $mod) {
            $this->line("  ✓ " . strtoupper($mod));
        }
        $this->info("Selected UI Framework: " . strtoupper($framework));
        $this->line('');

        // 1. Publish Master Config & Common Assets
        $this->comment('Publishing master configuration & frontend assets...');
        $this->callSilent('vendor:publish', [
            '--provider' => 'Ataurbdx\Authenticator\AuthenticatorServiceProvider',
            '--tag'      => 'authenticator-config',
            '--force'    => true,
        ]);
        $this->callSilent('vendor:publish', [
            '--provider' => 'Ataurbdx\Authenticator\AuthenticatorServiceProvider',
            '--tag'      => 'authenticator-assets',
            '--force'    => true,
        ]);
        $this->callSilent('vendor:publish', [
            '--provider' => 'Ataurbdx\Authenticator\AuthenticatorServiceProvider',
            '--tag'      => 'authenticator-service',
            '--force'    => true,
        ]);

        // 2. Publish Module-by-Module Components (Controllers, Views, Routes, Migrations)
        foreach ($selectedModules as $module) {
            $this->comment("Publishing module components for [{$module}] (Controllers, Views, Routes, Migrations)...");

            // Publish module bundle
            $this->callSilent('vendor:publish', [
                '--provider' => 'Ataurbdx\Authenticator\AuthenticatorServiceProvider',
                '--tag'      => "authenticator-{$module}",
                '--force'    => true,
            ]);
        }

        // 3. Ask to run migrations
        $this->line('');
        if ($this->confirm('Would you like to run the database migrations now?', true)) {
            $this->info('Running php artisan migrate...');
            $this->call('migrate');
        }

        $this->line('');
        $this->info('🎉 Authenticator installed successfully!');
        $this->info('Next steps:');
        $this->line('  1. Add [use Ataurbdx\\Authenticator\\Traits\\HasAuthenticator;] to your App\\Models\\User model.');
        $this->line('  2. Visit [/account], [/sign-in], or [/sign-up] to test the Auth UI.');
        $this->line('  3. Visit [/dashboard] or [/profile] to test the User Portal.');
        $this->line('  4. Routes published inside [routes/authenticator/] are ready to customize.');
        $this->line('  5. Controllers published inside [app/Http/Controllers/Authenticator/] are ready to customize.');

        return self::SUCCESS;
    }

    /**
     * Determine UI framework.
     */
    protected function determineFramework(): string
    {
        $framework = strtolower((string) $this->option('framework'));
        if (in_array($framework, ['tailwind', 'bootstrap', 'all'])) {
            return $framework;
        }

        if ($this->option('all')) {
            return 'tailwind';
        }

        return 'tailwind';
    }

    /**
     * Determine list of modules based on flags or interactive choice.
     */
    protected function determineModules(): array
    {
        // 1. Full suite shortcut
        if ($this->option('all') || $this->option('type') === 'all') {
            return ['core', 'profile', 'otp', '2fa', 'pin', 'social'];
        }

        // 2. Comma-separated types (e.g. --type=core,profile,otp)
        if ($type = $this->option('type')) {
            $types = array_map('trim', explode(',', strtolower($type)));
            if (!in_array('core', $types)) {
                array_unshift($types, 'core');
            }
            return array_unique($types);
        }

        // 3. Check individual flags
        $modules = ['core', 'profile']; // Default core and profile
        if ($this->option('otp')) $modules[] = 'otp';
        if ($this->option('2fa')) $modules[] = '2fa';
        if ($this->option('pin')) $modules[] = 'pin';
        if ($this->option('social')) $modules[] = 'social';

        if (count($modules) > 2) {
            return array_unique($modules);
        }

        // 4. Interactive choice
        $this->info('Select which modules you wish to enable for this project:');
        
        $choices = [
            'core'    => 'Core Auth (users, sign-in, sign-up, account gateway, password reset)',
            'profile' => 'User Portal (dashboard, profile management, 5 isolated credential forms)',
            'otp'     => 'OTP Verification (otp_codes, verify views, multi-channel)',
            '2fa'     => 'Two-Factor Authentication (user_2fa, TOTP QR setup, challenge)',
            'pin'     => 'PIN Content Lock (user_pins, screen lock privacy overlay)',
            'social'  => 'Social Accounts (OAuth providers, dynamic callback handlers)',
        ];

        $selected = $this->choice(
            'Choose modules (comma-separated numbers or names):',
            array_values($choices),
            null,
            null,
            true
        );

        $result = ['core', 'profile'];
        foreach ($selected as $item) {
            foreach ($choices as $key => $label) {
                if ($item === $label) {
                    $result[] = $key;
                }
            }
        }

        return array_unique($result);
    }
}
