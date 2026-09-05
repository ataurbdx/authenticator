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
     * Convenient accessor: $userSocial->provider returns "google".
     */
    public function getProviderAttribute(): string
    {
        return $this->socialProvider->provider ?? '';
    }
}
