<?php

namespace Ataurbdx\Authenticator\Modules\Otp\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserOtp extends Model
{
    protected $guarded = [];

    public function getTable()
    {
        return config('authenticator.tables.user_otps', 'user_otps');
    }

    protected function casts(): array
    {
        return [
            'data'        => 'array',
            'metadata'    => 'array',
            'expires_at'  => 'datetime',
            'verified_at' => 'datetime',
            'is_used'     => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        $userModel = config('auth.providers.users.model', 'App\\Models\\User');
        return $this->belongsTo($userModel, 'user_id');
    }

    /**
     * Check if OTP has expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
