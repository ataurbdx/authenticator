<?php

namespace Ataurbdx\Authenticator\Modules\Pin\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ataurbdx\Authenticator\Modules\Pin\Services\PinService;

class EnsurePinUnlocked
{
    protected PinService $pinService;

    public function __construct(PinService $pinService)
    {
        $this->pinService = $pinService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $this->pinService->isSessionLocked($user)) {
            // If request expects JSON (API or AJAX data fetch)
            if ($request->expectsJson()) {
                return response()->json([
                    'success'     => false,
                    'pin_locked'  => true,
                    'message'     => 'Screen is locked. Please enter your security PIN to view content.',
                    'code'        => 'PIN_CONTENT_LOCKED',
                ], 423); // 423 Locked
            }

            // For Web Blade views: Render the PIN lock overlay page while keeping current URL
            $viewName = view()->exists('authenticator.pin-lock') 
                ? 'authenticator.pin-lock' 
                : 'authenticator::pin-lock';

            return response()->view($viewName, [
                'intended_url' => $request->fullUrl(),
            ], 423);
        }

        return $next($request);
    }
}
