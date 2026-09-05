<?php

namespace Ataurbdx\Authenticator\Modules\TwoFactor\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequireTwoFactor
{
    /**
     * Handle an incoming request.
     * Intercepts authenticated users who have 2FA enabled until they pass the challenge.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            if (!empty($user->two_factor) && config('authenticator.modules.2fa', true)) {
                $is2faVerified = session('auth_2fa_verified', false);

                if (!$is2faVerified) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'success'             => false,
                            'requires_two_factor' => true,
                            'user_id'             => $user->id,
                            'message'             => 'Two-factor authentication challenge required.',
                            'code'                => 'TWO_FACTOR_REQUIRED',
                        ], 403);
                    }

                    session(['auth_2fa_user_id' => $user->id]);

                    return redirect()
                        ->route('authenticator.web.2fa', ['user_id' => $user->id]);
                }
            }
        }

        return $next($request);
    }
}
