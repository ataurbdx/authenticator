<?php

namespace Ataurbdx\Authenticator\Http\Controllers\Web\Authenticator;

use Illuminate\Routing\Controller;
use Ataurbdx\Authenticator\Modules\Socialite\Models\SocialProvider;
use Ataurbdx\Authenticator\Modules\Socialite\Models\UserSocial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * Allowed OAuth providers
     */
    protected array $allowedProviders = ['google', 'facebook', 'github', 'apple', 'twitter', 'linkedin'];

    /**
     * Resolve configured User model class
     */
    protected function getUserModel(): string
    {
        return config('auth.providers.users.model', \App\Models\User::class);
    }

    /**
     * Redirect to social provider (For guest login / registration)
     */
    public function redirect(string $provider)
    {
        $provider = strtolower($provider);

        if (!in_array($provider, $this->allowedProviders)) {
            return $this->handlePopupCallback('error', "Provider [{$provider}] is not supported.");
        }

        try {
            session(['social_auth_action' => 'login']);
            $this->ensureProviderConfigured($provider);

            return $this->getDriverWithAccountChooser($provider)
                ->stateless()
                ->redirect();
        } catch (\Throwable $e) {
            Log::error("Social redirect error ({$provider}): " . $e->getMessage());
            return $this->handlePopupCallback('error', "Social authentication for {$provider} is unavailable: " . $e->getMessage());
        }
    }

    /**
     * Connect social provider for authenticated users (From Profile Settings)
     */
    public function connect(string $provider)
    {
        $provider = strtolower($provider);

        if (!in_array($provider, $this->allowedProviders)) {
            return $this->handlePopupCallback('error', "Provider [{$provider}] is not supported.");
        }

        if (!Auth::check()) {
            return $this->handlePopupCallback('error', 'Please sign in first to connect a social account.');
        }

        $user = Auth::user();
        if ($user->hasSocial($provider)) {
            return $this->handlePopupCallback('success', 'This ' . ucfirst($provider) . ' account is already connected to your profile.');
        }

        try {
            session(['social_auth_action' => 'connect']);
            $this->ensureProviderConfigured($provider);

            return $this->getDriverWithAccountChooser($provider)
                ->stateless()
                ->redirect();
        } catch (\Throwable $e) {
            Log::error("Social connect redirect error ({$provider}): " . $e->getMessage());
            return $this->handlePopupCallback('error', "Could not initiate {$provider} connection.");
        }
    }

    /**
     * Handle social provider callback
     */
    public function callback(Request $request, string $provider)
    {
        $provider = strtolower($provider);

        try {
            $this->ensureProviderConfigured($provider);
            $socialUser = Socialite::driver($provider)->stateless()->user();
            $action = session()->pull('social_auth_action', 'login');

            // 1. Explicit connect flow from user profile
            if ($action === 'connect' && Auth::check()) {
                return $this->handleConnectFlow(Auth::user(), $provider, $socialUser);
            }

            // 2. Guest Login or Register flow
            return $this->handleLoginOrRegisterFlow($provider, $socialUser);

        } catch (\Throwable $e) {
            Log::error("Social callback error ({$provider}): " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine());
            return $this->handlePopupCallback('error', 'Authentication failed: ' . $e->getMessage());
        }
    }

    /**
     * Disconnect social account (from profile settings via AJAX)
     */
    public function disconnect(Request $request, string $provider)
    {
        $provider = strtolower($provider);
        $user = Auth::user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        $socialProvider = SocialProvider::where('provider', $provider)->first();
        if (!$socialProvider) {
            return response()->json(['success' => false, 'message' => 'Provider not recognized.'], 404);
        }

        $link = UserSocial::where('user_id', $user->id)
            ->where('social_provider_id', $socialProvider->id)
            ->first();

        if (!$link) {
            return response()->json(['success' => false, 'message' => 'This account is not linked.'], 400);
        }

        // Safety Guard: Cannot disconnect if user has no password and this is their only social account
        if (!$user->hasPassword() && $user->socials()->count() <= 1) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot disconnect your only login method. Please set a password first.',
            ], 422);
        }

        $wasPrimary = (bool) $link->primary;
        $link->delete();

        // Promote next available social account to primary if needed
        if ($wasPrimary) {
            $next = UserSocial::where('user_id', $user->id)->first();
            $next?->makePrimary();
        }

        return response()->json([
            'success' => true,
            'message' => ucfirst($provider) . ' account disconnected successfully.',
        ]);
    }

    /**
     * Guest Login/Register flow
     */
    protected function handleLoginOrRegisterFlow(string $provider, $socialUser)
    {
        $socialProvider = $this->resolveSocialProvider($provider);

        // 1. Existing social link found -> Login immediately
        $existingLink = UserSocial::where('social_provider_id', $socialProvider->id)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if ($existingLink) {
            Auth::login($existingLink->user, true);
            $targetUrl = session()->pull('url.intended') ?? config('authenticator.redirect_to', '/dashboard');
            return $this->handlePopupCallback('success', 'Signed in successfully with ' . ucfirst($provider) . '!', $targetUrl);
        }

        // 2. Existing user by email -> Auto-link and login
        $email = $socialUser->getEmail();
        $userModel = $this->getUserModel();
        if (!empty($email)) {
            $existingUser = $userModel::where('email', strtolower($email))->first();
            if ($existingUser) {
                return $this->linkSocialAccount($existingUser, $socialProvider, $socialUser, true);
            }
        }

        // 3. Brand new user -> Create user + social link
        $newUser = $this->createUserWithSocialAccount($socialProvider, $socialUser);
        Auth::login($newUser, true);
        $targetUrl = session()->pull('url.intended') ?? config('authenticator.redirect_to', '/dashboard');

        return $this->handlePopupCallback('success', 'Account created and signed in successfully!', $targetUrl);
    }

    /**
     * Connect flow for authenticated users (from profile settings)
     */
    protected function handleConnectFlow($loggedInUser, string $provider, $socialUser)
    {
        $socialProvider = $this->resolveSocialProvider($provider);

        // Check if this specific social account (provider_id) is linked to ANY user
        $existingLink = UserSocial::where('social_provider_id', $socialProvider->id)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if ($existingLink) {
            if ((int) $existingLink->user_id === (int) $loggedInUser->id) {
                return $this->handlePopupCallback('success', ucfirst($provider) . ' is already connected to your profile.', session()->pull('url.intended', null));
            }
            return $this->handlePopupCallback('error', 'This ' . ucfirst($provider) . ' account is already linked to another user.');
        }

        // User already has this provider linked with a different provider_id
        if ($loggedInUser->hasSocial($provider)) {
            return $this->handlePopupCallback('error', 'You already have a ' . ucfirst($provider) . ' account connected. Please disconnect it first.');
        }

        return $this->linkSocialAccount($loggedInUser, $socialProvider, $socialUser, false);
    }

    /**
     * Link social account to existing user
     */
    protected function linkSocialAccount($user, SocialProvider $socialProvider, $socialUser, bool $shouldLogin = true)
    {
        $hasPrimary = UserSocial::where('user_id', $user->id)->where('primary', true)->exists();

        $link = UserSocial::updateOrCreate(
            [
                'user_id'            => $user->id,
                'social_provider_id' => $socialProvider->id,
            ],
            [
                'provider_id'   => $socialUser->getId(),
                'email'         => $socialUser->getEmail(),
                'name'          => $socialUser->getName(),
                'avatar'        => $socialUser->getAvatar(),
                'provider_data' => (array) $socialUser->user,
            ]
        );

        if (!$hasPrimary) {
            $link->makePrimary();
        }

        // Update user avatar if empty
        if (empty($user->avatar) && !empty($socialUser->getAvatar())) {
            $user->update(['avatar' => $socialUser->getAvatar()]);
        }

        if ($shouldLogin) {
            Auth::login($user, true);
            $targetUrl = session()->pull('url.intended') ?? config('authenticator.redirect_to', '/dashboard');
            return $this->handlePopupCallback('success', 'Signed in successfully via ' . ucfirst($socialProvider->provider) . '!', $targetUrl);
        }

        // Connect mode: Keep user on current page (null triggers in-place reload, unless specific url.intended exists)
        $connectTarget = session()->pull('url.intended', null);
        return $this->handlePopupCallback('success', ucfirst($socialProvider->provider) . ' account connected successfully!', $connectTarget);
    }

    /**
     * Create brand new user from social account
     */
    protected function createUserWithSocialAccount(SocialProvider $socialProvider, $socialUser)
    {
        $userModel = $this->getUserModel();
        $fullName = $socialUser->getName() ?: ($socialUser->getNickname() ?: 'User');
        $nameParts = explode(' ', trim($fullName), 2);
        $email = $socialUser->getEmail() ?: 'user_' . $socialProvider->provider . '_' . $socialUser->getId() . '@temp.authenticator.local';

        $user = $userModel::create([
            'first_name'        => $nameParts[0] ?? 'User',
            'last_name'         => $nameParts[1] ?? '',
            'username'          => $this->generateUniqueUsername($fullName),
            'email'             => $email,
            'password'          => null,
            'email_verified_at' => now(),
            'avatar'            => $socialUser->getAvatar(),
            'status'            => true,
        ]);

        UserSocial::create([
            'user_id'            => $user->id,
            'social_provider_id' => $socialProvider->id,
            'provider_id'        => $socialUser->getId(),
            'email'              => $socialUser->getEmail(),
            'name'               => $socialUser->getName(),
            'avatar'             => $socialUser->getAvatar(),
            'provider_data'      => (array) $socialUser->user,
            'primary'            => true,
        ]);

        return $user;
    }

    /**
     * Force account selection screen (asset-sheba pattern)
     */
    protected function getDriverWithAccountChooser(string $provider)
    {
        $driver = Socialite::driver($provider);
        $params = match ($provider) {
            'google'   => ['prompt' => 'select_account'],
            'facebook' => ['auth_type' => 'rerequest', 'display' => 'popup'],
            'github'   => ['allow_signup' => 'false'],
            default    => ['prompt' => 'select_account'],
        };

        return $driver->with($params);
    }

    /**
     * Ensure provider credentials are setup in config and DB record exists
     */
    protected function ensureProviderConfigured(string $provider): SocialProvider
    {
        $socialProvider = SocialProvider::where('provider', $provider)->first();

        // If exists in DB with client_id, inject into config
        if ($socialProvider && !empty($socialProvider->client_id)) {
            $redirectUrl = !empty($socialProvider->redirect_url)
                ? $socialProvider->redirect_url
                : url("/authenticator/social/{$provider}/callback");

            config([
                "services.{$provider}" => [
                    'client_id'     => $socialProvider->client_id,
                    'client_secret' => $socialProvider->getDecryptedSecret() ?: $socialProvider->client_secret,
                    'redirect'      => $redirectUrl,
                ]
            ]);

            Log::info("Socialite provider [{$provider}] configured with redirect: {$redirectUrl}");

            return $socialProvider;
        }

        // Fallback: Check config/services.php
        $servicesConfig = config("services.{$provider}");
        if (!empty($servicesConfig['client_id'])) {
            return SocialProvider::updateOrCreate(
                ['provider' => $provider],
                [
                    'name'          => ucfirst($provider),
                    'client_id'     => $servicesConfig['client_id'],
                    'client_secret' => $servicesConfig['client_secret'] ?? '',
                    'redirect_url'  => $servicesConfig['redirect'] ?? url("/auth/social/{$provider}/callback"),
                    'is_active'     => true,
                ]
            );
        }

        // Auto-create stub in social_providers if not existing
        return SocialProvider::firstOrCreate(
            ['provider' => $provider],
            [
                'name'         => ucfirst($provider),
                'client_id'    => env(strtoupper($provider) . '_CLIENT_ID', ''),
                'client_secret'=> env(strtoupper($provider) . '_CLIENT_SECRET', ''),
                'redirect_url' => url("/auth/social/{$provider}/callback"),
                'is_active'    => true,
            ]
        );
    }

    /**
     * Resolve SocialProvider model instance
     */
    protected function resolveSocialProvider(string $provider): SocialProvider
    {
        return $this->ensureProviderConfigured($provider);
    }

    /**
     * Generate unique username
     */
    protected function generateUniqueUsername(string $name): string
    {
        $userModel = $this->getUserModel();
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

    /**
     * Render popup callback bridge view
     */
    protected function handlePopupCallback(string $status, string $message, ?string $redirectUrl = null)
    {
        $view = view()->exists('authenticator.social-callback')
            ? 'authenticator.social-callback'
            : (view()->exists('authenticator::social-callback')
                ? 'authenticator::social-callback'
                : 'authenticator::tailwind.social-callback');

        return view($view, [
            'status'      => $status,
            'message'     => $message,
            'redirectUrl' => $redirectUrl,
        ]);
    }
}
