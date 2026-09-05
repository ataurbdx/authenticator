<?php

namespace Ataurbdx\Authenticator\Modules\Socialite\Services;

use Ataurbdx\Authenticator\Modules\Socialite\Models\SocialProvider;
use Ataurbdx\Authenticator\Modules\Socialite\Models\UserSocial;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialiteService
{
    /**
     * Dynamically inject OAuth credentials from database into Laravel config at runtime.
     */
    public function configureDriver(string $providerName): SocialProvider
    {
        $provider = SocialProvider::where('provider', $providerName)
            ->where('is_active', true)
            ->first();

        if (!$provider) {
            throw new \RuntimeException("The social provider [{$providerName}] is not configured or is currently disabled.");
        }

        $redirectUrl = $provider->redirect_url ?: url(config('authenticator.routes.api_prefix', 'api/v1/auth') . "/social/{$providerName}/callback");

        config([
            "services.{$providerName}" => [
                'client_id'     => $provider->client_id,
                'client_secret' => $provider->getDecryptedSecret(),
                'redirect'      => $redirectUrl,
            ]
        ]);

        return $provider;
    }

    /**
     * Get the redirect response for social login.
     */
    public function getRedirect(string $providerName)
    {
        $provider = $this->configureDriver($providerName);
        $driver = Socialite::driver($providerName);

        if (!empty($provider->scopes)) {
            $driver->scopes($provider->scopes);
        }

        return $driver->redirect();
    }

    /**
     * Handle the OAuth provider callback and authenticate/provision the user.
     */
    public function handleCallback(string $providerName): array
    {
        $provider = $this->configureDriver($providerName);
        $socialUser = Socialite::driver($providerName)->user();

        // 1. If user is already authenticated -> Link account
        if (Auth::check()) {
            return $this->linkAccount(Auth::user(), $provider, $socialUser);
        }

        // 2. Existing social account -> Login
        $existingLink = UserSocial::where('social_provider_id', $provider->id)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if ($existingLink) {
            Auth::login($existingLink->user, true);
            return [
                'success' => true,
                'message' => 'Login successful.',
                'user'    => $existingLink->user,
            ];
        }

        // 3. Smart Link: Check if user exists with matching email
        $userModel = config('auth.providers.users.model', 'App\\Models\\User');
        $email = $socialUser->getEmail();

        if ($email && config('authenticator.social.auto_link_existing_email', true)) {
            $existingUser = $userModel::where('email', strtolower($email))->first();
            if ($existingUser) {
                return $this->linkAccount($existingUser, $provider, $socialUser);
            }
        }

        // 4. Create new user + social account
        return $this->createUserWithSocial($provider, $socialUser);
    }

    /**
     * Link social account to existing user.
     */
    public function linkAccount($user, SocialProvider $provider, $socialUser): array
    {
        $link = UserSocial::updateOrCreate(
            [
                'user_id'            => $user->id,
                'social_provider_id' => $provider->id,
            ],
            [
                'provider_id'   => $socialUser->getId(),
                'email'         => $socialUser->getEmail(),
                'name'          => $socialUser->getName(),
                'avatar'        => $socialUser->getAvatar(),
                'provider_data' => (array) $socialUser->user,
            ]
        );

        if (empty($user->user_social_id)) {
            $user->update(['user_social_id' => $link->id]);
        }

        Auth::login($user, true);

        return [
            'success' => true,
            'message' => "Successfully connected {$provider->name} account.",
            'user'    => $user,
        ];
    }

    /**
     * Create new user and attach social link.
     */
    protected function createUserWithSocial(SocialProvider $provider, $socialUser): array
    {
        $userModel = config('auth.providers.users.model', 'App\\Models\\User');

        $email = $socialUser->getEmail();
        if (!$email && config('authenticator.social.create_temp_email_if_missing', true)) {
            $email = 'social_' . Str::random(10) . '@temp.authenticator.local';
        }

        $fullName = $socialUser->getName() ?: 'User';
        $parts = explode(' ', trim($fullName), 2);

        $user = $userModel::create([
            'first_name'        => $parts[0] ?? 'User',
            'last_name'         => $parts[1] ?? '',
            'username'          => $this->generateUniqueUsername($fullName),
            'email'             => $email,
            'avatar'            => $socialUser->getAvatar(),
            'email_verified_at' => now(),
            'status'            => true,
        ]);

        $link = UserSocial::create([
            'user_id'            => $user->id,
            'social_provider_id' => $provider->id,
            'provider_id'        => $socialUser->getId(),
            'email'              => $socialUser->getEmail(),
            'name'               => $socialUser->getName(),
            'avatar'             => $socialUser->getAvatar(),
            'provider_data'      => (array) $socialUser->user,
        ]);

        $user->update(['user_social_id' => $link->id]);

        Auth::login($user, true);

        return [
            'success' => true,
            'message' => 'Account created and logged in successfully.',
            'user'    => $user,
        ];
    }

    /**
     * Generate unique username from name.
     */
    protected function generateUniqueUsername(string $name): string
    {
        $userModel = config('auth.providers.users.model', 'App\\Models\\User');
        $base = Str::slug($name, '');
        if (empty($base)) {
            $base = 'user';
        }

        $username = $base;
        $counter = 1;

        while ($userModel::where('username', $username)->exists()) {
            $username = $base . $counter++;
        }

        return $username;
    }
}
