<?php

namespace Ataurbdx\Authenticator\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Ataurbdx\Authenticator\Modules\Authenticator\Models\UserData;
use Ataurbdx\Authenticator\Modules\Pin\Models\UserPin;
use Ataurbdx\Authenticator\Modules\TwoFactor\Models\User2fa;
use Ataurbdx\Authenticator\Modules\Otp\Models\OtpCode;
use Ataurbdx\Authenticator\Modules\Socialite\Models\UserSocial;

trait HasAuthenticator
{
    use HasOtp;

    /**
     * Automatic Name Handling (Accessor + Mutator).
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => trim("{$this->first_name} {$this->last_name}"),
            set: function (?string $value) {
                $parts = explode(' ', trim((string) $value), 2);
                return [
                    'first_name' => $parts[0] ?? '',
                    'last_name'  => $parts[1] ?? '',
                ];
            }
        );
    }

    /**
     * Check if user has a standard password set.
     */
    public function hasPassword(): bool
    {
        return !empty($this->password);
    }

    /**
     * Check if user has a security PIN configured.
     */
    public function hasPin(): bool
    {
        return !empty($this->pin);
    }

    /**
     * Check if user is registered exclusively via social OAuth.
     */
    public function isSocialOnly(): bool
    {
        return empty($this->password) && $this->socials()->exists();
    }

    /**
     * Check if phone is verified.
     */
    public function isPhoneVerified(): bool
    {
        return !is_null($this->phone_verified_at);
    }

    /**
     * Mark user's phone as verified.
     */
    public function markPhoneAsVerified(): bool
    {
        return $this->forceFill(['phone_verified_at' => now()])->save();
    }

    /**
     * Check if email is verified.
     */
    public function isEmailVerified(): bool
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Mark user's email as verified.
     */
    public function markEmailAsVerified(): bool
    {
        return $this->forceFill(['email_verified_at' => now()])->save();
    }

    /**
     * Mark contact verified based on channel type.
     */
    public function markContactAsVerified(string $channel): bool
    {
        if (strtolower($channel) === 'email') {
            return $this->markEmailAsVerified();
        }

        return $this->markPhoneAsVerified();
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function data(): HasOne
    {
        return $this->hasOne(UserData::class, 'user_id');
    }

    public function pinSettings(): HasOne
    {
        return $this->hasOne(UserPin::class, 'user_id');
    }

    public function twoFa(): HasOne
    {
        return $this->hasOne(User2fa::class, 'user_id');
    }

    public function otps(): HasMany
    {
        return $this->hasMany(OtpCode::class, 'user_id');
    }

    public function socials(): HasMany
    {
        return $this->hasMany(UserSocial::class, 'user_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Universal scope to find by email, username, or phone.
     */
    public function scopeWhereIdentifier($query, string $identifier)
    {
        $clean = trim($identifier);
        return $query->where(function ($q) use ($clean) {
            $q->where('email', $clean)
              ->orWhere('username', $clean)
              ->orWhere('phone', $clean)
              ->orWhere('phone', ltrim($clean, '+'));
        });
    }

    /**
     * Scope to search by full combined name.
     */
    public function scopeWhereName($query, string $name)
    {
        return $query->whereRaw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) LIKE ?", ["%{$name}%"]);
    }

    /**
     * Fast indexed search across first_name and last_name.
     */
    public function scopeSearchName($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('first_name', 'like', "{$term}%")
              ->orWhere('last_name', 'like', "{$term}%");
        });
    }
}
