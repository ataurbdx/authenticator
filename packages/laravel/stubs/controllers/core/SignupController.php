<?php

namespace App\Http\Controllers\Authenticator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SignupController extends Controller
{
    protected $authService;

    public function __construct(\App\Services\AuthenticatorService $authService)
    {
        $this->authService = $authService;
    }

    public function showSignupForm(Request $request)
    {
        return view('authenticator.sign-up');
    }

    public function signup(Request $request)
    {
        // Prepare phone from code and number before validation
        if ($request->filled('number')) {
            $cc = trim($request->input('code', '+880'));
            $num = ltrim($request->input('number'), '0');
            $request->merge([
                'phone' => $cc . $num
            ]);
        }

        $request->validate([
            'first_name' => 'nullable|string|max:100',
            'last_name'  => 'nullable|string|max:100',
            'username'   => 'required|string|max:50|unique:users,username',
            'email'      => 'nullable|email|max:255|unique:users,email',
            'code'       => 'nullable|string|max:10',
            'number'     => 'nullable|numeric|digits:10',
            'phone'      => 'nullable|string|unique:users,phone',
            'password'   => 'required|string|min:8|confirmed',
        ]);

        if (!$request->filled('email') && !$request->filled('phone')) {
            return back()->withErrors(['email' => 'Either email or phone number is required.'])->withInput();
        }

        $phoneData = $request->filled('phone') ? $request->only('code', 'number') : null;

        $user = $this->authService->createUser($request->all(), $phoneData);
        auth()->login($user);

        return redirect()->intended(config('authenticator.redirect_to', '/dashboard'));
    }
}
