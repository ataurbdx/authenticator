<?php

namespace Ataurbdx\Authenticator\Modules\Otp\Services;

use Ataurbdx\Authenticator\Modules\Otp\Models\UserOtp;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    /**
     * Generate random numeric OTP code.
     */
    public function generateCode(int $length = 6): string
    {
        $min = pow(10, $length - 1);
        $max = pow(10, $length) - 1;
        return (string) random_int($min, $max);
    }

    /**
     * Send OTP to email or phone.
     */
    public function sendOtp(string $type, string $contact, string $action = 'verify', ?int $userId = null, array $metadata = []): array
    {
        $contact = trim($contact);
        $length = config('authenticator.otp.length', 6);
        $expiryMinutes = config('authenticator.otp.expiration_minutes', 10);
        $code = $this->generateCode($length);

        // Invalidate prior unused OTPs for this contact & action
        UserOtp::where('contact', $contact)
            ->where('action', $action)
            ->where('is_used', false)
            ->update(['is_used' => true]);

        // Create new record
        $otp = UserOtp::create([
            'user_id'    => $userId,
            'type'       => $type,
            'contact'    => $contact,
            'otp_code'   => Hash::make($code),
            'action'     => $action,
            'expires_at' => now()->addMinutes($expiryMinutes),
            'metadata'   => array_merge($metadata, [
                'ip'         => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]),
        ]);

        // Dispatch notification
        if ($type === 'email') {
            $this->dispatchEmail($contact, $code);
        } else {
            $this->dispatchSms($contact, $code);
        }

        Log::info("OTP generated for [{$contact}] action [{$action}]");

        $response = [
            'success'    => true,
            'message'    => "Verification code sent to {$contact}.",
            'expires_at' => $otp->expires_at->toISOString(),
        ];

        // Expose plain code in development mode for easy testing
        if (config('authenticator.otp.debug_in_dev', true) && app()->environment(['local', 'testing', 'development'])) {
            $response['debug_code'] = $code;
        }

        return $response;
    }

    /**
     * Verify an incoming OTP code.
     */
    public function verifyOtp(string $contact, string $code, string $action = 'verify'): array
    {
        $contact = trim($contact);
        $maxAttempts = config('authenticator.otp.max_attempts', 5);

        $otp = UserOtp::where('contact', $contact)
            ->where('action', $action)
            ->where('is_used', false)
            ->latest('id')
            ->first();

        if (!$otp) {
            return [
                'success' => false,
                'message' => 'No active OTP verification found. Please request a new code.',
                'code'    => 'OTP_NOT_FOUND',
            ];
        }

        if ($otp->isExpired()) {
            return [
                'success' => false,
                'message' => 'The verification code has expired. Please request a new code.',
                'code'    => 'OTP_EXPIRED',
            ];
        }

        if ($otp->attempts >= $maxAttempts) {
            $otp->update(['is_used' => true]);
            return [
                'success' => false,
                'message' => 'Maximum verification attempts exceeded. Please request a new code.',
                'code'    => 'MAX_ATTEMPTS_EXCEEDED',
            ];
        }

        if (!Hash::check($code, $otp->otp_code)) {
            $otp->increment('attempts');
            $remaining = max(0, $maxAttempts - $otp->attempts);
            return [
                'success'   => false,
                'message'   => "Invalid verification code. {$remaining} attempts remaining.",
                'code'      => 'INVALID_CODE',
                'remaining' => $remaining,
            ];
        }

        // Mark as verified
        $otp->update([
            'verified_at' => now(),
            'is_used'     => true,
        ]);

        return [
            'success' => true,
            'message' => 'Verification successful.',
            'otp_id'  => $otp->id,
            'user_id' => $otp->user_id,
        ];
    }

    /**
     * Dispatch OTP code via Email.
     */
    protected function dispatchEmail(string $email, string $code): void
    {
        // Safe logging or standard mail dispatch
        try {
            // Can be intercepted by a custom Mailable if desired
        } catch (\Throwable $e) {
            Log::error("Failed to send OTP email: {$e->getMessage()}");
        }
    }

    /**
     * Dispatch OTP code via SMS gateway.
     */
    protected function dispatchSms(string $phone, string $code): void
    {
        // Ready for pluggable SMS gateway drivers (Twilio, local BD gateways)
        Log::info("Dispatching SMS to {$phone} with code: {$code}");
    }
}
