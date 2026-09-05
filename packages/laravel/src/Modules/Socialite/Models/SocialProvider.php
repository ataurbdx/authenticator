<?php

namespace Ataurbdx\Authenticator\Modules\Socialite\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Crypt;

class SocialProvider extends Model
{
    protected $guarded = [];

    public function getTable()
    {
        return config('authenticator.tables.social_providers', 'social_providers');
    }

    protected function casts(): array
    {
        return [
            'scopes'    => 'array',
            'settings'  => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function userSocials(): HasMany
    {
        return $this->hasMany(UserSocial::class, 'social_provider_id');
    }

    /**
     * Decrypt the stored client secret.
     */
    public function getDecryptedSecret(): ?string
    {
        if (empty($this->client_secret)) {
            return null;
        }

        try {
            return Crypt::decryptString($this->client_secret);
        } catch (\Throwable $e) {
            // In case it was stored as plain text during dev/seeding
            return $this->client_secret;
        }
    }
}
