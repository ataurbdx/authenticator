<?php

namespace Ataurbdx\Authenticator\Modules\Pin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPin extends Model
{
    protected $guarded = [];

    public function getTable()
    {
        return config('authenticator.tables.user_pins', 'user_pins');
    }

    protected function casts(): array
    {
        return [
            'pin_enabled'      => 'boolean',
            'pin_locked_until' => 'datetime',
            'unlocked_at'      => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        $userModel = config('auth.providers.users.model', 'App\\Models\\User');
        return $this->belongsTo($userModel, 'user_id');
    }

    /**
     * Check if the user is currently locked out from entering PIN.
     */
    public function isLockedOut(): bool
    {
        return $this->pin_locked_until && $this->pin_locked_until->isFuture();
    }
}
