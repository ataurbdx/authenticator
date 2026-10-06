<?php

namespace Ataurbdx\Authenticator\Modules\Authenticator\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AuthenticatorSetting extends Model
{
    protected $guarded = ['id'];

    /**
     * Get the table associated with the model.
     */
    public function getTable()
    {
        return config('authenticator.tables.settings', 'authenticator_settings');
    }

    /**
     * Retrieve a setting value by key with optional default fallback.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        try {
            $setting = static::where('key', $key)->first();

            if (!$setting) {
                return $default;
            }

            return static::castValue($setting->value, $setting->type);
        } catch (\Throwable) {
            return $default;
        }
    }

    /**
     * Set or update a setting value.
     */
    public static function set(string $key, mixed $value, string $group = 'general', ?string $type = null): static
    {
        $resolvedType = $type ?: static::detectType($value);
        $storedValue = is_array($value) || is_object($value) ? json_encode($value) : (string) $value;

        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $storedValue,
                'group' => $group,
                'type'  => $resolvedType,
            ]
        );
    }

    /**
     * Check if account verification is currently mandatory before dashboard access.
     */
    public static function isVerificationMandatory(): bool
    {
        $dbSetting = static::get('registration_requires_verification');
        if ($dbSetting !== null) {
            return (bool) $dbSetting;
        }

        return (bool) config('authenticator.verification.mandatory', false);
    }

    /**
     * Dynamically resolve post-verification destination URL.
     * Hierarchy: 1. DB Setting -> 2. Config -> 3. App routes (dashboard / home) -> 4. Root '/'
     */
    public static function getRedirectUrl(?string $fallback = null): string
    {
        if ($fallback) {
            return $fallback;
        }

        // 1. Check database setting
        $dbRedirect = static::get('redirect_after_verification');
        if (!empty($dbRedirect)) {
            return \Illuminate\Support\Facades\Route::has($dbRedirect) ? route($dbRedirect) : url($dbRedirect);
        }

        // 2. Check config
        $configRedirect = config('authenticator.verification.redirect_after_verification');
        if (!empty($configRedirect)) {
            return \Illuminate\Support\Facades\Route::has($configRedirect) ? route($configRedirect) : url($configRedirect);
        }

        // 3. Fallback to existing application routes dynamically
        if (\Illuminate\Support\Facades\Route::has('dashboard')) {
            return route('dashboard');
        }
        if (\Illuminate\Support\Facades\Route::has('home')) {
            return route('home');
        }

        return url('/');
    }

    /**
     * Dynamically resolve verification notice/prompt route.
     */
    public static function getNoticeRoute(): string
    {
        $dbNotice = static::get('verification_notice_route');
        if (!empty($dbNotice)) {
            return $dbNotice;
        }

        return config('authenticator.verification.notice_route', 'authenticator.web.verify-otp');
    }

    /**
     * Dynamically resolve delivery mode ('code', 'link', 'both') for a given channel.
     */
    public static function getChannelMode(string $channel, ?string $fallback = null): string
    {
        $channel = strtolower(trim($channel));

        // 1. Channel specific DB setting (e.g. 'email_verification_mode')
        $mode = static::get("{$channel}_verification_mode");
        if (!empty($mode)) {
            return strtolower(trim($mode));
        }

        // 2. Global DB verification mode
        $globalMode = static::get('default_verification_mode');
        if (!empty($globalMode)) {
            return strtolower(trim($globalMode));
        }

        // 3. Config fallback
        return $fallback ?: (config("authenticator.otp.channel_modes.{$channel}") ?? config('authenticator.otp.default_mode', 'both'));
    }

    /**
     * Cast raw database value according to configured type.
     */
    protected static function castValue(mixed $value, string $type): mixed
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'boolean', 'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer', 'int'   => (int) $value,
            'json', 'array'    => json_decode($value, true) ?: [],
            default            => (string) $value,
        };
    }

    /**
     * Auto-detect type based on PHP variable type.
     */
    protected static function detectType(mixed $value): string
    {
        if (is_bool($value)) return 'boolean';
        if (is_int($value)) return 'integer';
        if (is_array($value) || is_object($value)) return 'json';
        return 'string';
    }
}
