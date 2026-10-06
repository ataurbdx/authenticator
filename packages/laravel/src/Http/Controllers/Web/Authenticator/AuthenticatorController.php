<?php

namespace Ataurbdx\Authenticator\Http\Controllers\Web\Authenticator;

use Illuminate\Routing\Controller;

class AuthenticatorController extends Controller
{
    /**
     * Resolve view: prioritize published view in resources/views/authenticator, fallback to package view.
     */
    protected function renderAuthView(string $viewName, array $data = [])
    {
        if (view()->exists("authenticator.{$viewName}")) {
            return view("authenticator.{$viewName}", $data);
        }

        return view("authenticator::{$viewName}", $data);
    }

    /**
     * Show Account View (Step 1 Identifier Detection).
     */
    public function showAccount()
    {
        return $this->renderAuthView('account');
    }

    /**
     * Show Sign-In View.
     */
    public function showSignIn()
    {
        return $this->renderAuthView('sign-in');
    }

    /**
     * Show Sign-Up View.
     */
    public function showSignUp()
    {
        return $this->renderAuthView('sign-up');
    }

    /**
     * Show Modal Partial View (for AJAX-rendered auth modals).
     */
    public function showAuthModal()
    {
        return $this->renderAuthView('auth-modal', [
            'isModal'    => true,
            'initialTab' => request('tab', 'account'),
        ]);
    }

    /**
     * Show Login View (backward-compatible alias to sign-in).
     */
    public function showLogin()
    {
        return $this->showSignIn();
    }

    /**
     * Show Registration View (backward-compatible alias to sign-up).
     */
    public function showRegister()
    {
        return $this->showSignUp();
    }

    /**
     * Show OTP Verification View.
     */
    public function showVerifyOtp()
    {
        return $this->renderAuthView('verify-otp');
    }

    /**
     * Show Two-Factor Challenge View.
     */
    public function showTwoFactor()
    {
        return $this->renderAuthView('two-factor');
    }

    /**
     * Show PIN Setup View.
     */
    public function showSetPin()
    {
        return $this->renderAuthView('set-pin');
    }

    /**
     * Show Password Reset View.
     */
    public function showResetPassword()
    {
        return $this->renderAuthView('reset-password');
    }

    /**
     * Handle Direct Verification Link confirmation.
     */
    public function verifyLink(\Illuminate\Http\Request $request, string $token)
    {
        $otpService = app('authenticator.otp');
        $result = $otpService->verifyByToken($token);

        if (!$result['success']) {
            if ($request->wantsJson()) {
                return response()->json($result, 422);
            }

            return $this->renderAuthView('verify-status', [
                'success' => false,
                'message' => $result['message'],
            ]);
        }

        // Auto-login user if valid user is returned and not already logged in
        if (!empty($result['user']) && !auth()->check()) {
            auth()->login($result['user']);
        }

        if ($request->wantsJson()) {
            return response()->json($result);
        }

        $redirectUrl = \Ataurbdx\Authenticator\Modules\Authenticator\Models\AuthenticatorSetting::getRedirectUrl();

        return redirect()->intended($redirectUrl)->with('success', 'Verification confirmed successfully!');
    }
}
