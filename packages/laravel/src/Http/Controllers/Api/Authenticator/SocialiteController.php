<?php

namespace Ataurbdx\Authenticator\Http\Controllers\Api\Authenticator;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Ataurbdx\Authenticator\Modules\Socialite\Services\SocialiteService;
use Ataurbdx\Authenticator\Modules\Socialite\Models\SocialProvider;

class SocialiteController extends Controller
{
    protected SocialiteService $socialiteService;

    public function __construct(SocialiteService $socialiteService)
    {
        $this->socialiteService = $socialiteService;
    }

    /**
     * Get list of active social providers for dynamic frontend buttons.
     */
    public function providers()
    {
        $providers = SocialProvider::where('is_active', true)
            ->select(['id', 'provider', 'name', 'icon', 'button_color'])
            ->get();

        return response()->json([
            'success'   => true,
            'providers' => $providers,
        ]);
    }

    /**
     * Redirect to OAuth provider.
     */
    public function redirect($provider)
    {
        try {
            return $this->socialiteService->getRedirect($provider);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Handle OAuth provider callback.
     */
    public function callback(Request $request, $provider)
    {
        try {
            $result = $this->socialiteService->handleCallback($provider);

            // If request expects HTML / browser redirect
            if (!$request->expectsJson()) {
                return redirect()->intended('/')->with('success', $result['message']);
            }

            return response()->json($result);
        } catch (\Throwable $e) {
            if (!$request->expectsJson()) {
                return redirect('/auth/login')->with('error', 'Social login failed: ' . $e->getMessage());
            }

            return response()->json([
                'success' => false,
                'message' => 'Social login failed: ' . $e->getMessage(),
            ], 400);
        }
    }
}
