<?php

namespace App\Http\Controllers\Authenticator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SigninController extends Controller
{
    protected $authService;

    public function __construct(\App\Services\AuthenticatorService $authService)
    {
        $this->authService = $authService;
    }

    public function showSigninForm(Request $request)
    {
        $type = $request->query('type', 'email');
        if ($type === 'phone') {
            $identifier = $request->query('number');
        } else {
            $identifier = $request->query($type);
        }
        
        return view('authenticator.sign-in', compact('identifier', 'type'));
    }

    public function signin(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'code'       => 'nullable|string',
            'password'   => 'required|string',
        ]);

        $identifier = $request->identifier;
        if ($request->filled('code') && preg_match('/^[0-9\s\-]+$/', $identifier)) {
            $identifier = $request->code . ltrim($identifier, '0');
        }

        if ($this->authService->attemptSignin($identifier, $request->password, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(config('authenticator.redirect_to', '/dashboard'));
        }

        return back()->withErrors([
            'identifier' => 'These credentials do not match our records.',
        ])->withInput($request->only('identifier', 'code', 'type'));
    }

    public function signout(Request $request)
    {
        auth()->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('authenticator.account');
    }
}
