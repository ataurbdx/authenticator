<?php

namespace Ataurbdx\Authenticator\Http\Controllers\Api\Authenticator;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Ataurbdx\Authenticator\Modules\TwoFactor\Services\TwoFactorService;

class TwoFactorController extends Controller
{
    protected TwoFactorService $twoFactorService;

    public function __construct(TwoFactorService $twoFactorService)
    {
        $this->twoFactorService = $twoFactorService;
    }

    /**
     * Initialize 2FA setup (returns QR URI & secret).
     */
    public function setup(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $data = $this->twoFactorService->setupTwoFactor($user);

        return response()->json([
            'success' => true,
            'message' => 'Scan the QR code with Google Authenticator or enter the secret manually.',
            'data'    => $data,
        ]);
    }

    /**
     * Confirm 2FA setup with user's first 6-digit code.
     */
    public function confirm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $confirmed = $this->twoFactorService->confirmTwoFactor($user, $request->code);

        if (!$confirmed) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid authentication code. Please try again.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Two-Factor Authentication is now active on your account.',
        ]);
    }

    /**
     * Verify 2FA challenge during login.
     */
    public function verifyChallenge(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'code'    => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $userModel = config('auth.providers.users.model', 'App\\Models\\User');
        $user = $userModel::find($request->user_id);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found.'], 404);
        }

        $valid = $this->twoFactorService->verifyOrUseRecoveryCode($user, $request->code);

        if (!$valid) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid authentication or recovery code.',
            ], 422);
        }

        Auth::login($user, true);
        session(['auth_2fa_verified' => true]);
        session()->forget('auth_2fa_user_id');

        $token = null;
        if (method_exists($user, 'createToken')) {
            $token = $user->createToken('auth_token')->plainTextToken;
        }

        return response()->json([
            'success' => true,
            'message' => 'Two-factor verification successful. Logged in.',
            'user'    => $user,
            'token'   => $token,
        ]);
    }
}
