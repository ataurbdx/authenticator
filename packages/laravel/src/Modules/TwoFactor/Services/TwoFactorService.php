<?php

namespace Ataurbdx\Authenticator\Modules\TwoFactor\Services;

use Ataurbdx\Authenticator\Modules\TwoFactor\Models\User2fa;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class TwoFactorService
{
    protected string $base32Chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Generate random Base32 secret string (16 or 32 characters).
     */
    public function generateSecretKey(int $length = 32): string
    {
        $secret = '';
        for ($i = 0; $i < $length; $i++) {
            $secret .= $this->base32Chars[random_int(0, 31)];
        }
        return $secret;
    }

    /**
     * Generate list of random 8-character recovery backup codes.
     */
    public function generateRecoveryCodes(int $count = 8): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(Str::random(10));
        }
        return $codes;
    }

    /**
     * Build the standard otpauth:// URL for QR code generation.
     */
    public function getQrCodeUri(string $secret, string $userEmail): string
    {
        $issuer = config('authenticator.two_factor.issuer', config('app.name', 'Authenticator'));
        return sprintf(
            'otpauth://totp/%s:%s?secret=%s&issuer=%s&algorithm=SHA1&digits=6&period=30',
            rawurlencode($issuer),
            rawurlencode($userEmail),
            $secret,
            rawurlencode($issuer)
        );
    }

    /**
     * Verify a 6-digit TOTP code against a Base32 secret (with window allowance).
     */
    public function verifyCode(string $secret, string $code, int $window = 1): bool
    {
        $code = trim($code);
        if (strlen($code) !== 6) {
            return false;
        }

        $currentTimeSlice = floor(time() / 30);

        for ($i = -$window; $i <= $window; $i++) {
            $calculatedCode = $this->calculateTotpCode($secret, $currentTimeSlice + $i);
            if (hash_equals($calculatedCode, $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Calculate 6-digit TOTP code for given time slice (RFC 6238).
     */
    protected function calculateTotpCode(string $secret, int $timeSlice): string
    {
        $secretKey = $this->base32Decode($secret);
        $time = pack('N*', 0) . pack('N*', $timeSlice);
        $hmac = hash_hmac('sha1', $time, $secretKey, true);
        $offset = ord($hmac[19]) & 0xf;
        $hashPart = substr($hmac, $offset, 4);
        $value = unpack('N', $hashPart)[1] & 0x7fffffff;
        return str_pad((string) ($value % 1000000), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Decode a Base32 encoded string into raw binary.
     */
    protected function base32Decode(string $b32): string
    {
        $b32 = strtoupper($b32);
        $buffer = 0;
        $bufferSize = 0;
        $binary = '';

        for ($i = 0; $i < strlen($b32); $i++) {
            $char = $b32[$i];
            $val = strpos($this->base32Chars, $char);
            if ($val === false) {
                continue;
            }
            $buffer = ($buffer << 5) | $val;
            $bufferSize += 5;
            if ($bufferSize >= 8) {
                $bufferSize -= 8;
                $binary .= chr(($buffer >> $bufferSize) & 0xff);
            }
        }

        return $binary;
    }

    /**
     * Setup or initialize 2FA for a user (generates secret & backup codes).
     */
    public function setupTwoFactor($user): array
    {
        $secret = $this->generateSecretKey(32);
        $recoveryCodes = $this->generateRecoveryCodes(config('authenticator.two_factor.recovery_codes_count', 8));

        User2fa::updateOrCreate(
            ['user_id' => $user->id],
            [
                'totp_secret'  => Crypt::encryptString($secret),
                'backup_codes' => Crypt::encryptString(json_encode($recoveryCodes)),
                'confirmed_at' => null,
            ]
        );

        $qrUri = $this->getQrCodeUri($secret, $user->email ?? $user->username ?? 'user');

        return [
            'secret'         => $secret,
            'qr_uri'         => $qrUri,
            'recovery_codes' => $recoveryCodes,
        ];
    }

    /**
     * Confirm 2FA setup by verifying the user's first valid code.
     */
    public function confirmTwoFactor($user, string $code): bool
    {
        $twoFa = User2fa::where('user_id', $user->id)->first();
        if (!$twoFa) {
            return false;
        }

        $secret = $twoFa->getDecryptedSecret();
        if (!$secret || !$this->verifyCode($secret, $code)) {
            return false;
        }

        $twoFa->update(['confirmed_at' => now()]);
        $user->update(['two_factor' => true]);

        return true;
    }

    /**
     * Verify either a TOTP code OR consume a single-use emergency recovery backup code.
     */
    public function verifyOrUseRecoveryCode($user, string $input): bool
    {
        $twoFa = User2fa::where('user_id', $user->id)->whereNotNull('confirmed_at')->first();
        if (!$twoFa) {
            return false;
        }

        // Try standard TOTP code first
        $secret = $twoFa->getDecryptedSecret();
        if ($secret && $this->verifyCode($secret, $input)) {
            return true;
        }

        // Try recovery backup codes
        $backupCodes = $twoFa->getDecryptedBackupCodes();
        $inputUpper = strtoupper(trim($input));

        foreach ($backupCodes as $index => $code) {
            if (hash_equals($code, $inputUpper)) {
                // Consume backup code
                unset($backupCodes[$index]);
                $twoFa->setEncryptedBackupCodes($backupCodes);
                return true;
            }
        }

        return false;
    }
}
