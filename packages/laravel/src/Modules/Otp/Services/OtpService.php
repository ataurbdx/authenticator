<?php

namespace Ataurbdx\Authenticator\Modules\Otp\Services;

use Ataurbdx\Authenticator\Modules\Otp\Models\OtpCode;
use Ataurbdx\Authenticator\Modules\Otp\Models\OtpChannel;
use Ataurbdx\Authenticator\Modules\Authenticator\Models\AuthenticatorSetting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OtpService
{
    /**
     * Generate secure random numeric OTP code.
     */
    public function generateCode(int $length = 6): string
    {
        $min = (int) pow(10, $length - 1);
        $max = (int) pow(10, $length) - 1;
        return (string) random_int($min, $max);
    }

    /**
     * Universal OTP & Direct Link Sender:
     * Dispatches OTP code and/or direct verification link to ANY contact across ANY delivery channel for ANY business purpose.
     *
     * @param string $contact Recipient email, phone number, etc.
     * @param string $purpose Business purpose (e.g. 'registration', 'login_2fa', 'vault_unlock', 'order_approval')
     * @param string|null $channel Delivery channel ('email', 'phone', etc.). Auto-detected if null.
     * @param int|null $userId Optional associated user ID (nullable for guests or custom actions).
     * @param array $metadata Extra contextual payload (ip, device, action metadata).
     * @param string|null $mode Delivery mode ('code', 'link', 'both'). Resolves from DB channel/settings if null.
     * @param mixed|null $verifiable Optional polymorphic model instance (User, Order, Admin, etc.)
     */
    public function send(
        string $contact,
        string $purpose = 'verification',
        ?string $channel = null,
        ?int $userId = null,
        array $metadata = [],
        ?string $mode = null,
        mixed $verifiable = null
    ): array {
        $contact = trim($contact);
        $purpose = trim($purpose);

        // 1. Auto-detect delivery channel if not explicitly specified
        if (empty($channel)) {
            $defaultChannel = AuthenticatorSetting::get('default_otp_channel') ?: config('authenticator.otp.default_channel', 'email');
            $channel = str_contains($contact, '@') ? 'email' : $defaultChannel;
        }
        $channel = strtolower(trim($channel));

        // 2. Resolve target channel from otp_channels table
        $channelModel = $this->resolveChannel($channel);

        // 3. Dynamically resolve delivery mode ('code', 'link', 'both'):
        // Priority 1: Request parameter/metadata -> Priority 2: otp_channels table settings -> Priority 3: authenticator_settings DB -> Priority 4: Config
        if (empty($mode)) {
            $mode = $metadata['mode'] ?? null;
        }
        if (empty($mode) && $channelModel) {
            $mode = $channelModel->getDeliveryMode();
        }
        if (empty($mode)) {
            $mode = AuthenticatorSetting::getChannelMode($channel);
        }
        $mode = strtolower(trim((string) $mode));

        // 4. Dynamic OTP length and expiration from DB settings with config fallback
        $length = (int) (AuthenticatorSetting::get('otp_length') ?: config('authenticator.otp.length', 6));
        $expiryMinutes = (int) (AuthenticatorSetting::get('otp_expiration_minutes') ?: config('authenticator.otp.expiration_minutes', 10));
        $code = $this->generateCode($length);
        $rawToken = Str::random(64);

        // 3. Generate verification link & shorten if shortener is configured
        $verificationUrl = $this->generateVerificationUrl($rawToken, $contact, $purpose);
        $link = $this->shortenUrl($verificationUrl);

        // 4. Invalidate prior unused OTPs for this contact and purpose to avoid replay
        OtpCode::where('contact', $contact)
            ->where('purpose', $purpose)
            ->where('is_used', false)
            ->update(['is_used' => true]);

        // 5. Create single hybrid active record in otp_codes table
        $otp = OtpCode::create([
            'contact'         => $contact,
            'channel'         => $channel,
            'code'            => Hash::make($code),
            'token'           => $rawToken,
            'delivery_mode'   => $mode,
            'purpose'         => $purpose,
            'user_id'         => $userId ?: ($verifiable && method_exists($verifiable, 'getKey') && is_numeric($verifiable->getKey()) ? $verifiable->getKey() : null),
            'verifiable_type' => $verifiable ? get_class($verifiable) : null,
            'verifiable_id'   => $verifiable?->getKey(),
            'expires_at'      => now()->addMinutes($expiryMinutes),
            'attempts'        => 0,
            'is_used'         => false,
            'metadata'        => array_merge($metadata, [
                'ip'         => request()?->ip(),
                'user_agent' => request()?->userAgent(),
                'mode'       => $mode,
                'link'       => $link,
            ]),
        ]);

        // 6. Dispatch verification code and/or link to recipient via resolved channel
        $this->dispatch($channel, $contact, $code, $purpose, $metadata, $link, $mode);

        Log::info("OTP/Link generated for [{$contact}] purpose [{$purpose}] channel [{$channel}] mode [{$mode}] ID [{$otp->id}]");

        $response = [
            'success'          => true,
            'message'          => "Verification details sent via {$channel} to {$contact}.",
            'channel'          => $channel,
            'purpose'          => $purpose,
            'delivery_mode'    => $mode,
            'expires_at'       => $otp->expires_at->toISOString(),
            'otp_id'           => $otp->id,
            'verification_url' => $link,
        ];

        // Include raw code in debug/local mode for automated testing or headless development
        if (config('authenticator.otp.debug_in_dev', true) && app()->environment(['local', 'testing', 'development'])) {
            $response['debug_code'] = $code;
            $response['debug_token'] = $rawToken;
        }

        return $response;
    }

    /**
     * Universal OTP Verifier:
     * Validates incoming OTP code against the otp_codes table for any business purpose.
     */
    public function verify(string $contact, string $code, string $purpose = 'verification'): array
    {
        $contact = trim($contact);
        $purpose = trim($purpose);
        $maxAttempts = (int) config('authenticator.otp.max_attempts', 5);

        $otp = OtpCode::where('contact', $contact)
            ->where('purpose', $purpose)
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

        if ($otp->hasExceededAttempts($maxAttempts)) {
            $otp->update(['is_used' => true]);
            return [
                'success' => false,
                'message' => 'Maximum verification attempts exceeded. Please request a new code.',
                'code'    => 'MAX_ATTEMPTS_EXCEEDED',
            ];
        }

        if (!Hash::check($code, $otp->code)) {
            $otp->increment('attempts');
            $remaining = max(0, $maxAttempts - $otp->attempts);
            return [
                'success'   => false,
                'message'   => "Invalid verification code. {$remaining} attempts remaining.",
                'code'      => 'INVALID_CODE',
                'remaining' => $remaining,
            ];
        }

        // Successfully verified
        $otp->update([
            'verified_at' => now(),
            'is_used'     => true,
        ]);

        Log::info("OTP successfully verified for [{$contact}] purpose [{$purpose}] ID [{$otp->id}]");

        return [
            'success'     => true,
            'message'     => 'Verification successful.',
            'otp_id'      => $otp->id,
            'contact'     => $contact,
            'purpose'     => $purpose,
            'channel'     => $otp->channel,
            'user_id'     => $otp->user_id,
            'verified_at' => $otp->verified_at->toISOString(),
        ];
    }

    /**
     * Dispatch notification across target channel with hybrid Code + Link support.
     */
    public function dispatch(
        string $channel,
        string $contact,
        string $code,
        string $purpose = 'verification',
        array $metadata = [],
        ?string $link = null,
        string $mode = 'both'
    ): void {
        switch (strtolower($channel)) {
            case 'phone':
            case 'sms':
                $this->dispatchPhone($contact, $code, $purpose, $link, $mode);
                break;
            case 'email':
            default:
                $this->dispatchEmail($contact, $code, $purpose, $link, $mode);
                break;
        }
    }

    /**
     * Universal Direct Link Verifier:
     * Validates incoming verification token against the otp_codes table.
     */
    public function verifyByToken(string $token, ?string $purpose = null): array
    {
        $token = trim($token);

        $otp = OtpCode::byToken($token)
            ->where('is_used', false)
            ->latest('id')
            ->first();

        if (!$otp) {
            return [
                'success' => false,
                'message' => 'Invalid or already used verification link.',
                'code'    => 'INVALID_LINK',
            ];
        }

        if ($purpose && $otp->purpose !== $purpose) {
            return [
                'success' => false,
                'message' => 'The verification link does not match the requested purpose.',
                'code'    => 'PURPOSE_MISMATCH',
            ];
        }

        if ($otp->isExpired()) {
            return [
                'success' => false,
                'message' => 'The verification link has expired. Please request a new one.',
                'code'    => 'LINK_EXPIRED',
            ];
        }

        // Successfully verified
        $otp->update([
            'verified_at' => now(),
            'is_used'     => true,
        ]);

        // Auto mark contact as verified on associated user if applicable
        $user = $otp->user;
        if (!$user && $otp->verifiable_type && class_exists($otp->verifiable_type)) {
            $user = $otp->verifiable;
        }

        if ($user) {
            if ($otp->channel === 'email' && method_exists($user, 'isEmailVerified') && !$user->isEmailVerified()) {
                $user->email_verified_at = now();
                $user->save();
            } elseif (in_array($otp->channel, ['sms', 'phone']) && method_exists($user, 'isPhoneVerified') && !$user->isPhoneVerified()) {
                $user->phone_verified_at = now();
                $user->save();
            }
        }

        Log::info("OTP link successfully verified for [{$otp->contact}] purpose [{$otp->purpose}] ID [{$otp->id}]");

        return [
            'success'     => true,
            'message'     => 'Verification link confirmed successfully.',
            'otp_id'      => $otp->id,
            'contact'     => $otp->contact,
            'purpose'     => $otp->purpose,
            'channel'     => $otp->channel,
            'user'        => $user,
            'user_id'     => $otp->user_id,
            'verified_at' => $otp->verified_at->toISOString(),
        ];
    }

    /**
     * Generate direct verification URL.
     */
    public function generateVerificationUrl(string $token, string $contact, string $purpose): string
    {
        try {
            if (\Illuminate\Support\Facades\Route::has('authenticator.web.verify-link')) {
                return route('authenticator.web.verify-link', ['token' => $token]);
            }
        } catch (\Throwable) {}

        $prefix = config('authenticator.routes.web_prefix', 'auth');
        return url("{$prefix}/verify-link/{$token}");
    }

    /**
     * Shorten URL via configured shortener or return as-is.
     */
    public function shortenUrl(string $url): string
    {
        $shortener = config('authenticator.otp.shortener');
        if (is_callable($shortener)) {
            try {
                return call_user_func($shortener, $url);
            } catch (\Throwable $e) {
                Log::warning("Shortener failed: {$e->getMessage()}. Falling back to full URL.");
            }
        }
        return $url;
    }

    /**
     * Backward-compatible alias for send().
     */
    public function sendOtp(string $type, string $contact, string $action = 'verify', ?int $userId = null, array $metadata = []): array
    {
        return $this->send($contact, $action, $type, $userId, $metadata);
    }

    /**
     * Backward-compatible alias for verify().
     */
    public function verifyOtp(string $contact, string $code, string $action = 'verify'): array
    {
        return $this->verify($contact, $code, $action);
    }

    /**
     * Resolve OTP channel configuration (Priority 1: DB credentials, Priority 2: Morphic gateway, Priority 3: .env).
     */
    public function resolveChannel(string $type, ?string $name = null): ?OtpChannel
    {
        if (!class_exists(OtpChannel::class)) {
            return null;
        }

        try {
            $settingName = $name ?: config('authenticator.otp.channel_setting_name', 'authenticator');

            // 1. Look up by specific Name if specified
            $channel = OtpChannel::active()->where('name', $settingName)->first();
            if ($channel) {
                return $channel;
            }

            // 2. Look up first active channel for this type
            return OtpChannel::active()->where('type', $type)->first();
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Dispatch OTP code and/or Link via Email.
     */
    protected function dispatchEmail(string $email, string $code, string $purpose = 'verification', ?string $link = null, string $mode = 'both'): void
    {
        try {
            $appName = config('app.name', 'Laravel');
            $subject = config('authenticator.otp.email_subject', "Your {$appName} Verification Code");
            $expiryMinutes = config('authenticator.otp.expiration_minutes', 10);
            $purposeLabel = ucwords(str_replace(['_', '-'], ' ', $purpose));

            // Hybrid Message construction based on Mode
            if ($mode === 'link' && $link) {
                $messageBody = "Hello,\n\nPlease verify your account for {$purposeLabel} by clicking the secure link below:\n\n{$link}\n\nThis link will expire in {$expiryMinutes} minutes.\n\nRegards,\n{$appName} Security Team";
            } elseif ($mode === 'code') {
                $messageBody = "Hello,\n\nYour security verification code for {$purposeLabel} is:\n\n{$code}\n\nThis code will expire in {$expiryMinutes} minutes.\n\nRegards,\n{$appName} Security Team";
            } else {
                // 'both'
                $messageBody = "Hello,\n\nYour security verification code for {$purposeLabel} is:\n\n{$code}\n\nOr click the link below to verify directly:\n{$link}\n\nThis verification will expire in {$expiryMinutes} minutes.\n\nRegards,\n{$appName} Security Team";
            }

            $channel = $this->resolveChannel('email');

            // 1. Dynamic database SMTP credentials if present
            if ($channel) {
                $credentials = $channel->getResolvedCredentials($channel->name);

                if (!empty($credentials['host']) && !empty($credentials['username'])) {
                    config([
                        'mail.mailers.authenticator_dynamic' => [
                            'transport'  => $credentials['driver'] ?? $credentials['transport'] ?? 'smtp',
                            'host'       => $credentials['host'],
                            'port'       => (int) ($credentials['port'] ?? 587),
                            'encryption' => $credentials['encryption'] ?? 'tls',
                            'username'   => $credentials['username'],
                            'password'   => $credentials['password'] ?? '',
                            'timeout'    => null,
                        ],
                    ]);

                    $fromAddress = $credentials['from_address'] ?? $credentials['from'] ?? config('mail.from.address');
                    $fromName    = $credentials['from_name'] ?? $credentials['name'] ?? $appName;

                    Mail::mailer('authenticator_dynamic')->raw($messageBody, function ($message) use ($email, $subject, $appName, $fromAddress, $fromName) {
                        $message->to($email)
                                ->from($fromAddress, $fromName)
                                ->subject("[{$appName}] {$subject}");
                    });

                    Log::info("OTP email successfully dispatched to [{$email}] for [{$purpose}] mode [{$mode}] via dynamic database SMTP [{$channel->name}]");
                    return;
                }
            }

            // 2. Default Laravel mailer fallback
            Mail::raw($messageBody, function ($message) use ($email, $subject, $appName) {
                $message->to($email)
                        ->subject("[{$appName}] {$subject}");
            });

            Log::info("OTP email successfully dispatched to [{$email}] for [{$purpose}] mode [{$mode}] via default Laravel mailer");
        } catch (\Throwable $e) {
            Log::error("Failed to send OTP email to [{$email}] for [{$purpose}]: {$e->getMessage()}");
        }
    }

    /**
     * Dispatch OTP code and/or Link via Phone (SMS gateway or webhook).
     */
    protected function dispatchPhone(string $phone, string $code, string $purpose = 'verification', ?string $link = null, string $mode = 'both'): void
    {
        try {
            $appName = config('app.name', 'Laravel');
            $purposeLabel = ucwords(str_replace(['_', '-'], ' ', $purpose));

            if ($mode === 'link' && $link) {
                $message = "Verify your {$appName} account: {$link}";
            } elseif ($mode === 'code') {
                $message = "Your {$appName} code for {$purposeLabel} is {$code}. Valid for 10 minutes.";
            } else {
                $message = "Your {$appName} code is {$code} or verify directly: {$link}";
            }

            $channel = $this->resolveChannel('phone') ?: $this->resolveChannel('sms');
            $credentials = $channel ? $channel->getResolvedCredentials($channel->name) : [];
            $settings = $channel?->settings ?? [];

            $apiUrl = $credentials['url'] ?? $settings['url'] ?? null;
            if ($apiUrl) {
                $payload = [
                    'to'      => $phone,
                    'message' => $message,
                    'code'    => $code,
                    'link'    => $link,
                    'purpose' => $purpose,
                ];

                if (!empty($credentials['api_key'])) {
                    $payload['api_key'] = $credentials['api_key'];
                }

                $method = strtolower($settings['method'] ?? 'post');
                if ($method === 'get') {
                    Http::timeout(10)->get($apiUrl, $payload);
                } else {
                    Http::timeout(10)->post($apiUrl, $payload);
                }
            }

            Log::info("Phone OTP dispatched to [{$phone}] for [{$purpose}] mode [{$mode}] via channel: " . ($channel->name ?? 'default'));
        } catch (\Throwable $e) {
            Log::error("Failed to send Phone OTP to [{$phone}] for [{$purpose}]: {$e->getMessage()}");
        }
    }
}
