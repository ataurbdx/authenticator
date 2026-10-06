<?php

namespace Ataurbdx\Authenticator\Modules\Authenticator\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Ataurbdx\Authenticator\Modules\Authenticator\Models\AuthenticatorSetting;

class EnsureContactIsVerified
{
    /**
     * Handle an incoming request.
     *
     * If admin setting defines verification as mandatory, unverified users
     * are blocked from accessing protected routes (e.g. Dashboard) and
     * redirected to the verification view until verified.
     */
    public function handle(Request $request, Closure $next, ?string $redirectToRoute = null): Response
    {
        // 1. If verification is NOT mandatory in settings, bypass immediately
        if (!AuthenticatorSetting::isVerificationMandatory()) {
            return $next($request);
        }

        $user = $request->user();

        // 2. If unauthenticated, pass along to auth middleware
        if (!$user) {
            return $next($request);
        }

        // 3. Check if user's primary contact is verified
        $isVerified = false;

        if (!empty($user->email)) {
            $isVerified = method_exists($user, 'isEmailVerified') ? $user->isEmailVerified() : !is_null($user->email_verified_at);
        } elseif (!empty($user->phone)) {
            $isVerified = method_exists($user, 'isPhoneVerified') ? $user->isPhoneVerified() : !is_null($user->phone_verified_at);
        } else {
            $isVerified = true; // Guest or username-only user
        }

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
