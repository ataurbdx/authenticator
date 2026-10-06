<?php

namespace Ataurbdx\Authenticator\Modules\Socialite\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSocial extends Model
{
    protected $guarded = [];

    public function getTable()
    {
        return config('authenticator.tables.user_socials', 'user_socials');
    }

    protected function casts(): array
    {
        return [
            'provider_data' => 'array',
            'primary' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        $userModel = config('auth.providers.users.model', 'App\\Models\\User');
        return $this->belongsTo($userModel, 'user_id');
    }

    public function socialProvider(): BelongsTo
    {
        return $this->belongsTo(SocialProvider::class, 'social_provider_id');
    }

    /**
     * Scope a query to only include primary social profiles.
     */
    public function scopePrimary($query)
    {
        return $query->where('primary', true);
    }

    /**
     * Check if this profile is marked as the primary social profile.
     */
    public function isPrimary(): bool
    {
        return (bool) $this->primary;
    }

    /**
     * Mark this social account as primary and unset primary for other accounts of this user.
     */
    public function makePrimary(): self
    {
        static::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['primary' => false]);

        $this->update(['primary' => true]);

        return $this;
    }

    /**
     * Convenient accessor: $userSocial->provider returns "google".
     */
    public function getProviderAttribute(): string
    {
        return $this->socialProvider->provider ?? '';
    }
}

