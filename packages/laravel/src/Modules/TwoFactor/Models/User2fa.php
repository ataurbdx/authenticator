<?php

namespace Ataurbdx\Authenticator\Modules\TwoFactor\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class User2fa extends Model
{
    protected $guarded = [];

    public function getTable()
    {
        return config('authenticator.tables.user_2fa', 'user_2fa');
    }

    protected function casts(): array
    {
        return [
            'confirmed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        $userModel = config('auth.providers.users.model', 'App\\Models\\User');
        return $this->belongsTo($userModel, 'user_id');
    }

    /**
     * Decrypt the TOTP secret key.
     */
    public function getDecryptedSecret(): ?string
    {
        if (empty($this->totp_secret)) {
            return null;
        }

        try {
            return Crypt::decryptString($this->totp_secret);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Get decrypted recovery backup codes.
     */
    public function getDecryptedBackupCodes(): array
    {
        if (empty($this->backup_codes)) {
            return [];
        }

        try {
            return json_decode(Crypt::decryptString($this->backup_codes), true) ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Save newly encrypted recovery backup codes.
     */
    public function setEncryptedBackupCodes(array $codes): void
    {
        $this->update([
            'backup_codes' => Crypt::encryptString(json_encode(array_values($codes))),
        ]);
    }
}
