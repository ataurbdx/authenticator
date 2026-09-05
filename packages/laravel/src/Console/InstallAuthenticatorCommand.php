<?php

namespace Ataurbdx\Authenticator\Console;

use Illuminate\Console\Command;

class InstallAuthenticatorCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'authenticator:install
                            {--type= : Specific module to install (core, otp, 2fa, pin, social, all)}
                            {--framework= : CSS Framework for Auth views (tailwind, bootstrap, all)}
                            {--all : Install all 5 modules without interactive prompt}
                            {--otp : Include OTP Verification module}
                            {--2fa : Include Two-Factor Authentication module}
                            {--pin : Include PIN Content Lock module}
                            {--social : Include Social Accounts module}';

    /**
     * The console command description.
     */
    protected $description = 'Install and configure Authenticator modules on-demand with Tailwind or Bootstrap 5 views';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('====================================================');
        $this->info('  🚀  AUTHENTICATOR — ON-DEMAND MODULAR INSTALLER  ');
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

        // 1. Publish Master Config
        $this->comment('Publishing master configuration...');
        $this->callSilent('vendor:publish', [
            '--provider' => 'Ataurbdx\Authenticator\AuthenticatorServiceProvider',
            '--tag'      => 'authenticator-config',
            '--force'    => true,
        ]);

        // 2. Publish Views and Assets based on Framework
        $this->comment("Publishing {$framework} views directly to resources/views/auth/...");
        if ($framework === 'bootstrap') {
            $this->callSilent('vendor:publish', [
                '--provider' => 'Ataurbdx\Authenticator\AuthenticatorServiceProvider',
                '--tag'      => 'authenticator-views-bootstrap',
                '--force'    => true,
            ]);
        } else {
            $this->callSilent('vendor:publish', [
                '--provider' => 'Ataurbdx\Authenticator\AuthenticatorServiceProvider',
                '--tag'      => 'authenticator-views-tailwind',
                '--force'    => true,
            ]);
        }
        $this->callSilent('vendor:publish', [
            '--provider' => 'Ataurbdx\Authenticator\AuthenticatorServiceProvider',
            '--tag'      => 'authenticator-assets',
            '--force'    => true,
        ]);

        // 3. Publish Module Migrations based on selected tags
        foreach ($selectedModules as $module) {
            $tag = "migrations-{$module}";
            $this->comment("Publishing migrations for module [{$module}]...");
            $this->callSilent('vendor:publish', [
                '--provider' => 'Ataurbdx\Authenticator\AuthenticatorServiceProvider',
                '--tag'      => $tag,
            ]);
        }

        // 4. Ask to run migrations
        if ($this->confirm('Would you like to run the published database migrations now?', true)) {
            $this->info('Running php artisan migrate...');
            $this->call('migrate');
        }

        $this->line('');
        $this->info('🎉 Authenticator installed successfully!');
        $this->info('Next steps:');
        $this->line('  1. Add [use HasAuthenticator;] to your App\\Models\\User model.');
        $this->line('  2. Visit [/account], [/sign-in], or [/sign-up] to test the Auth UI.');
        $this->line('  3. In your Blade templates, embed auth anywhere:');
        $this->line('     - Sign-In Form:  @include(\'auth.forms.sign-in-form\')');
        $this->line('     - Sign-Up Form:  @include(\'auth.forms.sign-up-form\')');
        $this->line('     - Auth Modal:    @include(\'auth.auth-modal\') and trigger window.openAuthModal()');
        $this->line('  4. API endpoints are ready at [/api/v1/auth/*].');

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

        return $this->choice(
            'Which CSS framework should Authenticator UI use?',
            ['tailwind' => 'Tailwind CSS (Modern Dark/Light)', 'bootstrap' => 'Bootstrap 5 (Clean Glassmorphic)', 'all' => 'Both'],
            'tailwind'
        );
    }

    /**
     * Determine list of modules based on flags or interactive choice.
     */
    protected function determineModules(): array
    {
        // 1. Full suite shortcut
        if ($this->option('all') || $this->option('type') === 'all') {
            return ['core', 'pin', 'otp', '2fa', 'social'];
        }

        // 2. Comma-separated types (e.g. --type=core,otp,pin)
        if ($type = $this->option('type')) {
            $types = array_map('trim', explode(',', strtolower($type)));
            if (!in_array('core', $types)) {
                array_unshift($types, 'core'); // Core is always required
            }
            return array_unique($types);
        }

        // 3. Check individual flags
        $modules = ['core']; // Always include core
        if ($this->option('otp')) $modules[] = 'otp';
        if ($this->option('2fa')) $modules[] = '2fa';
        if ($this->option('pin')) $modules[] = 'pin';
        if ($this->option('social')) $modules[] = 'social';

        if (count($modules) > 1) {
            return $modules;
        }

        // 4. Interactive choice
        $this->info('Select which modules you wish to enable for this project:');
        
        $choices = [
            'core'   => 'Core Auth (users, user_data, password login, Bootstrap UI)',
            'otp'    => 'OTP Verification (user_otps, email/phone verification, passwordless login)',
            '2fa'    => 'Two-Factor Authentication (user_2fa, Google Authenticator TOTP)',
            'pin'    => 'PIN Content Lock (user_pins, screen lock privacy overlay)',
            'social' => 'Social Accounts (social_providers, user_socials, Google/Facebook OAuth)',
        ];

        $selected = $this->choice(
            'Choose modules (comma-separated numbers or names):',
            array_values($choices),
            null,
            null,
            true
        );

        $result = ['core'];
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
