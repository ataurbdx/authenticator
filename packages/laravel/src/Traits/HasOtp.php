<?php

namespace Ataurbdx\Authenticator\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Ataurbdx\Authenticator\Modules\Otp\Models\OtpCode;

trait HasOtp
{
    /**
     * Polymorphic relation to all OTP codes associated with this model.
     */
    public function otps(): MorphMany
    {
        return $this->morphMany(OtpCode::class, 'verifiable');
    }

    /**
     * Send OTP or verification link for this model.
     *
     * @param string|null $channel Target channel ('email', 'sms', 'whatsapp', 'telegram'). Auto-detected if null.
     * @param string $purpose Business purpose identifier (e.g. 'verification', 'order_approval', 'vault_unlock').
     * @param string|null $mode Delivery mode ('code', 'link', 'both'). Resolved from config if null.
     * @param string|null $contact Recipient override (defaults to $this->phone or $this->email).
     * @param array $metadata Additional metadata.
     */
    public function sendOtp(
        ?string $channel = null,
        string $purpose = 'verification',
        ?string $mode = null,
        ?string $contact = null,
        array $metadata = []
    ): array {
        $contact = $contact ?: ($this->phone ?? $this->email ?? null);

        if (empty($contact)) {
            throw new \InvalidArgumentException("Cannot dispatch OTP: Model does not have a valid 'phone' or 'email' attribute.");
        }

        $otpService = app('authenticator.otp');

        return $otpService->send(
            contact: $contact,
            purpose: $purpose,
            channel: $channel,
            userId: $this->id ?? null,
            metadata: $metadata,
            mode: $mode,
            verifiable: $this
        );
    }

    /**
     * Verify OTP code or token for this model.
     */
    public function verifyOtp(string $codeOrToken, string $purpose = 'verification'): array
    {
        $contact = $this->phone ?? $this->email ?? null;
        $otpService = app('authenticator.otp');

        // Check if input looks like a 40-64 char token or standard code
        if (strlen($codeOrToken) >= 32) {
            return $otpService->verifyByToken($codeOrToken, $purpose);
        }

        return $otpService->verify(
            contact: (string) $contact,
            code: $codeOrToken,
            purpose: $purpose
        );
    }
}
