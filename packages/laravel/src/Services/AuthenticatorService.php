<?php

namespace Ataurbdx\Authenticator\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthenticatorService
{
    /**
     * Resolve the configured User model class.
     */
    protected function getUserModel(): string
    {
        return config('auth.providers.users.model', 'App\\Models\\User');
    }

    /**
     * Determine if the identifier is an email, phone, or username.
     */
    public function determineIdentifierType(string $identifier): string
    {
        $identifier = trim($identifier);

        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            return 'email';
        }

        $cleanPhone = preg_replace('/[\s\-\(\)]+/', '', $identifier);
        if (preg_match('/^\+?[0-9]{7,15}$/', $cleanPhone)) {
            return 'phone';
        }

        return 'username';
    }

    /**
     * Alias for determineIdentifierType.
     */
    public function detectIdentifierType(string $identifier): string
    {
        return $this->determineIdentifierType($identifier);
    }

    /**
     * Attempt to sign the user in using email, phone, or username.
     */
    public function attemptSignin(string $identifier, string $password, bool $remember = false): bool
    {
        $userModel = $this->getUserModel();

        $user = $userModel::where('email', $identifier)
            ->orWhere('phone', $identifier)
            ->orWhere('username', $identifier)
            ->first();

        if ($user && Hash::check($password, $user->password)) {
            auth()->login($user, $remember);
            return true;
        }

        return false;
    }

    /**
     * Check if a user exists based on an identifier and return user and type.
     */
    public function checkUserExists(string $identifier): array
    {
        $userModel = $this->getUserModel();
        $type = $this->determineIdentifierType($identifier);

        if ($type === 'email') {
            $identifier = strtolower(trim($identifier));
        } elseif ($type === 'phone') {
            $identifier = preg_replace('/[\s\-\(\)]+/', '', $identifier);
        } else {
            $identifier = strtolower(trim($identifier));
        }

        $userQuery = $userModel::query();
        if ($type === 'email') {
            $userQuery->where('email', $identifier);
        } elseif ($type === 'phone') {
            $userQuery->where('phone', $identifier);
        } else {
            $userQuery->where('username', $identifier);
        }
        $user = $userQuery->first();

        if (!$user) {
            $user = $userModel::where('email', $identifier)
                ->orWhere('phone', $identifier)
                ->orWhere('username', $identifier)
                ->first();
        }

        return [
            'user'       => $user,
            'type'       => $type,
            'identifier' => $identifier
        ];
    }

    /**
     * Create a new user record.
     */
    public function createUser(array $data, ?array $phoneData = null)
    {
        $userModel = $this->getUserModel();

        return $userModel::create([
            'first_name' => $data['first_name'] ?? null,
            'last_name'  => $data['last_name'] ?? null,
            'username'   => $data['username'] ?? null,
            'email'      => !empty($data['email']) ? strtolower(trim($data['email'])) : null,
            'phone'      => $data['phone'] ?? null,
            'phone_data' => $phoneData ? json_encode($phoneData) : null,
            'password'   => !empty($data['password']) ? Hash::make($data['password']) : null,
        ]);
    }
}
