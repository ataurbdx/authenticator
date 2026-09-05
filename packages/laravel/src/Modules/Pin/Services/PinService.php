<?php

namespace Ataurbdx\Authenticator\Modules\Pin\Services;

use Ataurbdx\Authenticator\Modules\Pin\Models\UserPin;
use Illuminate\Support\Facades\Hash;

class PinService
{
    /**
     * Set or update user security PIN.
     */
    public function setPin($user, string $pin): bool
    {
        $hashedPin = Hash::make($pin);
        $user->update(['pin' => $hashedPin]);

        UserPin::updateOrCreate(
            ['user_id' => $user->id],
            [
                'pin_enabled'      => true,
                'pin_attempts'     => 0,
                'pin_locked_until' => null,
                'unlocked_at'      => now(),
            ]
        );

        $this->unlockSession($user);

        return true;
    }

    /**
     * Verify the entered PIN.
     */
    public function verifyPin($user, string $pin): array
    {
        if (empty($user->pin)) {
            return [
                'success' => false,
                'message' => 'No security PIN is configured for this account.',
                'code'    => 'PIN_NOT_SET',
            ];
        }

        $settings = UserPin::firstOrCreate(
            ['user_id' => $user->id],
            ['pin_enabled' => true, 'pin_timeout' => config('authenticator.pin.default_timeout_minutes', 15)]
        );

        // Check if currently locked out
        if ($settings->isLockedOut()) {
            $minutes = $settings->pin_locked_until->diffInMinutes(now()) + 1;
            return [
                'success' => false,
                'message' => "Too many failed attempts. PIN entry is locked for {$minutes} minutes.",
                'code'    => 'PIN_LOCKED_OUT',
            ];
        }

        $maxAttempts = config('authenticator.pin.max_attempts', 5);

        if (!Hash::check($pin, $user->pin)) {
            $settings->increment('pin_attempts');

            if ($settings->pin_attempts >= $maxAttempts) {
                $lockoutMinutes = config('authenticator.pin.lockout_minutes', 15);
                $settings->update([
                    'pin_locked_until' => now()->addMinutes($lockoutMinutes),
                    'pin_attempts'     => 0,
                ]);

                return [
                    'success' => false,
                    'message' => "Maximum attempts reached. PIN locked for {$lockoutMinutes} minutes.",
                    'code'    => 'PIN_MAX_ATTEMPTS',
                ];
            }

            $remaining = $maxAttempts - $settings->pin_attempts;
            return [
                'success'   => false,
                'message'   => "Incorrect PIN. {$remaining} attempts remaining.",
                'code'      => 'INVALID_PIN',
                'remaining' => $remaining,
            ];
        }

        // Success - reset attempts and unlock
        $settings->update([
            'pin_attempts'     => 0,
            'pin_locked_until' => null,
            'unlocked_at'      => now(),
        ]);

        $this->unlockSession($user);

        return [
            'success' => true,
            'message' => 'PIN verified. Screen unlocked.',
        ];
    }

    /**
     * Check if user's session is currently PIN locked.
     */
    public function isSessionLocked($user): bool
    {
        if (!$user || empty($user->pin)) {
            return false;
        }

        $settings = UserPin::where('user_id', $user->id)->first();
        if (!$settings || !$settings->pin_enabled) {
            return false;
        }

        $unlockedAt = session('auth_pin_unlocked_at');
        if (!$unlockedAt) {
            return true;
        }

        $timeoutMinutes = $settings->pin_timeout ?: config('authenticator.pin.default_timeout_minutes', 15);
        return now()->diffInMinutes($unlockedAt) >= $timeoutMinutes;
    }

    /**
     * Unlock the PIN session in state.
     */
    public function unlockSession($user): void
    {
        session(['auth_pin_unlocked_at' => now()]);
    }

    /**
     * Manually lock the screen session.
     */
    public function lockSession($user): void
    {
        session()->forget('auth_pin_unlocked_at');
    }
}
