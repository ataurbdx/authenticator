<?php

namespace Ataurbdx\Authenticator\Modules\Auth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserData extends Model
{
    protected $guarded = [];

    public function getTable()
    {
        return config('authenticator.tables.user_data', 'user_data');
    }

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        $userModel = config('auth.providers.users.model', 'App\\Models\\User');
        return $this->belongsTo($userModel, 'user_id');
    }
}
