<?php

namespace App\Http\Controllers\Authenticator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('authenticator.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['identifier' => 'required']);

        // Default handler sends notification or OTP
        return back()->with('status', 'We have sent your password reset instructions if the account exists.');
    }
}
