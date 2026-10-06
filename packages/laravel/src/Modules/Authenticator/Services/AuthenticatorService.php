<?php

namespace Ataurbdx\Authenticator\Modules\Authenticator\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthenticatorService
{
    /**
     * Determine whether the given input is an email, phone, or username.
     */
    public function detectIdentifierType(string $input): string
    {
        $input = trim($input);

        if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            return 'email';
        }

        // Check if string contains digits and optional leading plus
        if (preg_match('/^\+?[0-9]{7,15}$/', preg_replace('/[\s\-\(\)]/', '', $input))) {
            return 'phone';
        }

        return 'username';
    }

    /**
     * Normalize phone numbers (stripping spaces, handling country codes).
     */
    public function normalizePhone(string $phone, string $countryCode = '+880'): string
    {
        $digits = preg_replace('/[^0-9]/', '', $phone);
        $cleanCountryCode = preg_replace('/[^0-9]/', '', $countryCode);

        // If phone already starts with the country code digits
        if (str_starts_with($digits, $cleanCountryCode)) {
            return '+' . $digits;
        }

        // Strip leading zero if present (e.g. 01780... -> 1780...)
        if (str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        return '+' . $cleanCountryCode . $digits;
    }

    /**
     * Find a user by any supported identifier (email, username, or phone).
     */
    public function findByIdentifier(string $identifier, ?string $countryCode = null)
    {
        $userModel = config('auth.providers.users.model', 'App\\Models\\User');
        $type = $this->detectIdentifierType($identifier);

        if ($type === 'email') {
            return $userModel::where('email', strtolower(trim($identifier)))->first();
        }

        if ($type === 'phone') {
            $normalized = $this->normalizePhone($identifier, $countryCode ?? '+880');
            $user = $userModel::where('phone', $normalized)->first();
            if (!$user) {
                // Fallback without leading +
                $user = $userModel::where('phone', ltrim($normalized, '+'))->first();
            }
            return $user;
        }

        return $userModel::where('username', trim($identifier))->first();
    }

    /**
     * Attempt login with credentials.
     */
    public function attempt(string $identifier, string $password, bool $remember = false, ?string $countryCode = null): array
    {
        $user = $this->findByIdentifier($identifier, $countryCode);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'No account found with these credentials.',
                'code'    => 'USER_NOT_FOUND',
            ];
        }

        if (!$user->status) {
            return [
                'success' => false,
                'message' => 'This account has been deactivated. Please contact support.',
                'code'    => 'ACCOUNT_DEACTIVATED',
            ];
        }

        if (empty($user->password)) {
            return [
                'success' => false,
                'message' => 'This account is set up with social or OTP login. Please login using that method.',
                'code'    => 'PASSWORD_NOT_SET',
            ];
        }

        if (!Hash::check($password, $user->password)) {
            return [
                'success' => false,
                'message' => 'The provided password is incorrect.',
                'code'    => 'INVALID_PASSWORD',
            ];
        }

        // Check if Two-Factor Authentication is enforced
        if (!empty($user->two_factor) && config('authenticator.modules.2fa', true)) {
            return [
                'success'             => true,
                'requires_two_factor' => true,
                'user_id'             => $user->id,
                'message'             => 'Please complete two-factor authentication.',
            ];
        }

        // Web session login
        Auth::login($user, $remember);

        // If Sanctum API token is available
        $token = null;
        if (method_exists($user, 'createToken')) {
            $token = $user->createToken('auth_token')->plainTextToken;
        }

        return [
            'success' => true,
            'message' => 'Login successful.',
            'user'    => $user,
            'token'   => $token,
        ];
    }

    /**
     * Register a new user account.
     */
    public function register(array $data): array
    {
        $userModel = config('auth.providers.users.model', 'App\\Models\\User');

        $phone = null;
        $phoneData = null;
        if (!empty($data['phone'])) {
            $countryCode = $data['country_code'] ?? '+880';
            $phone = $this->normalizePhone($data['phone'], $countryCode);
            $phoneData = [
                'country_code' => $countryCode,
                'number'       => preg_replace('/[^0-9]/', '', $data['phone']),
            ];
        }

        $user = $userModel::create([
            'first_name' => $data['first_name'] ?? null,
            'last_name'  => $data['last_name'] ?? null,
            'username'   => $data['username'] ?? null,
            'email'      => !empty($data['email']) ? strtolower(trim($data['email'])) : null,
            'email_data' => $data['email_data'] ?? null,
            'phone'      => $phone,
            'phone_data' => $phoneData,
            'password'   => !empty($data['password']) ? Hash::make($data['password']) : null,
            'pin'        => !empty($data['pin']) ? Hash::make($data['pin']) : null,
            'status'     => true,
        ]);

        // Auto-create UserData record
        if (class_exists(\Ataurbdx\Authenticator\Modules\Authenticator\Models\UserData::class)) {
            \Ataurbdx\Authenticator\Modules\Authenticator\Models\UserData::create([
                'user_id' => $user->id,
                'about'   => $data['about'] ?? null,
            ]);
        }

        // Login new user
        Auth::login($user, true);

        $token = null;
        if (method_exists($user, 'createToken')) {
            $token = $user->createToken('auth_token')->plainTextToken;
        }

        // Check if registration verification is mandatory from Admin Settings
        $requiresVerification = \Ataurbdx\Authenticator\Modules\Authenticator\Models\AuthenticatorSetting::isVerificationMandatory();
        $verificationResult = null;

        if ($requiresVerification && config('authenticator.modules.otp', true)) {
            $contact = $user->email ?: $user->phone;
            $channel = !empty($user->email) ? 'email' : 'phone';

            if ($contact) {
                try {
                    $otpService = app('authenticator.otp');
                    $verificationResult = $otpService->send(
                        contact: $contact,
                        purpose: 'registration',
                        channel: $channel,
                        userId: $user->id,
                        verifiable: $user
                    );
                } catch (\Throwable) {}
            }
        }

        return [
            'success'               => true,
            'message'               => $requiresVerification ? 'Registration successful. Please verify your account.' : 'Registration successful.',
            'user'                  => $user,
            'token'                 => $token,
            'requires_verification' => $requiresVerification,
            'verification'          => $verificationResult,
        ];
    }

    /**
     * Reset user password after OTP verification.
     */
    public function resetPassword(string $identifier, string $code, string $newPassword, ?string $countryCode = null): array
    {
        $contact = trim($identifier);
        if ($this->detectIdentifierType($contact) === 'phone') {
            $contact = $this->normalizePhone($contact, $countryCode ?? '+880');
        }

        // Verify OTP code
        $otpService = app('authenticator.otp');
        $verifyResult = $otpService->verifyOtp($contact, $code, 'reset_password');

        if (!$verifyResult['success']) {
            return $verifyResult;
        }

        $user = $this->findByIdentifier($contact, $countryCode);
        if (!$user) {
            return [
                'success' => false,
                'message' => 'No account found with this contact information.',
                'code'    => 'USER_NOT_FOUND',
            ];
        }

        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        return [
            'success' => true,
            'message' => 'Your password has been successfully reset. You may now log in with your new password.',
        ];
    }
}
