<?php

namespace Ataurbdx\Authenticator\Modules\Otp\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class OtpCode extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'expires_at'  => 'datetime',
        'verified_at' => 'datetime',
        'is_used'     => 'boolean',
        'metadata'    => 'array',
        'attempts'    => 'integer',
    ];

    /**
     * Get the table associated with the model.
     */
    public function getTable()
    {
        return config('authenticator.tables.otp_codes', 'otp_codes');
    }

    /**
     * Polymorphic relation to any verifiable target entity (User, Order, Admin, etc.).
     */
    public function verifiable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Associated user if available (nullable for guests / sensitive actions).
     */
    public function user(): BelongsTo
    {
        $userModel = config('auth.providers.users.model', 'App\\Models\\User');
        return $this->belongsTo($userModel, 'user_id');
    }

    /**
     * Scope query to active (unused and unexpired) OTP codes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_used', false)
                     ->where('expires_at', '>', now());
    }

    /**
     * Scope query by verification token.
     */
    public function scopeByToken($query, string $token)
    {
        return $query->where('token', $token);
    }

    /**
     * Scope query by business purpose.
     */
    public function scopeForPurpose($query, string $purpose)
    {
        return $query->where('purpose', $purpose);
    }

    /**
     * Check if OTP code has expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at ? $this->expires_at->isPast() : true;
    }

    /**
     * Check if OTP has exceeded max failed attempts.
     */
    public function hasExceededAttempts(int $maxAttempts = 5): bool
    {
        return $this->attempts >= $maxAttempts;
    }
}
