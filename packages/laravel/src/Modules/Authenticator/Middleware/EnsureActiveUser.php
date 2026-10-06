<?php

namespace Ataurbdx\Authenticator\Modules\Authenticator\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureActiveUser
{
    /**
     * Handle an incoming request.
     * Checks if the authenticated user account is active.
     * If banned or inactive (status === false), logs them out immediately.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            if (isset($user->status) && !$user->status) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Your account has been deactivated. Please contact support.',
                        'code'    => 'ACCOUNT_DEACTIVATED',
                    ], 403);
                }

                return redirect()
                    ->route('authenticator.web.sign-in')
                    ->with('error', 'Your account has been deactivated. Please contact support.');
            }
        }

        return $next($request);
    }
}
