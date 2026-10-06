<?php

namespace Ataurbdx\Authenticator\Modules\Authenticator\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Ataurbdx\Authenticator\Traits\HasAuthenticator;

class User extends Authenticatable
{
    use Notifiable, HasAuthenticator;

    protected $guarded = [];

    public function getTable()
    {
        return config('authenticator.tables.users', 'users');
    }

    protected $hidden = [
        'password',
        'pin',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password'          => 'hashed',
            'email_data'        => 'array',
            'phone_data'        => 'array',
            'two_factor'        => 'boolean',
            'status'            => 'boolean',
        ];
    }
}
