<?php

namespace Ataurbdx\Authenticator\Http\Middleware\Authenticator;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Ataurbdx\Authenticator\Modules\Authenticator\Models\AuthenticatorSetting;

class EnsureAuthenticatorVerified
{
    /**
     * Handle an incoming request.
     *
     * Middleware Alias: 'authenticator.verified'
     */
    public function handle(Request $request, Closure $next, ?string $redirectToRoute = null): Response
    {
        // 1. If admin has NOT made verification mandatory, pass through immediately
        if (!AuthenticatorSetting::isVerificationMandatory()) {
            return $next($request);
        }

        $user = $request->user();

        // 2. If unauthenticated, let standard 'auth' middleware handle it
        if (!$user) {
            return $next($request);
        }

        // 3. Check if user's registered contact is verified
        $isVerified = false;

        if (!empty($user->email)) {
            $isVerified = method_exists($user, 'isEmailVerified') ? $user->isEmailVerified() : !is_null($user->email_verified_at);
        } elseif (!empty($user->phone)) {
            $isVerified = method_exists($user, 'isPhoneVerified') ? $user->isPhoneVerified() : !is_null($user->phone_verified_at);
        } else {
            $isVerified = true;
        }

        // 4. If not verified, block and redirect to verification view
        if (!$isVerified) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success'  => false,
                    'message'  => 'Your account must be verified before accessing this area.',
                    'verified' => false,
                ], 403);
            }

            $route = $redirectToRoute ?: AuthenticatorSetting::getNoticeRoute();

            return (\Illuminate\Support\Facades\Route::has($route) ? redirect()->route($route) : redirect($route))
                ->with('warning', 'Please verify your email or phone number to access this area.');
        }

        return $next($request);
    }
}
