<?php

namespace Ataurbdx\Authenticator\Http\Controllers\Web\Authenticator;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class OtpVerificationController extends Controller
{
    public function showVerifyForm(Request $request)
    {
        return view('authenticator::verify');
    }

    public function sendOtp(Request $request)
    {
        return back()->with('status', 'A new OTP code has been sent!');
    }

    public function confirmOtp(Request $request)
    {
        return redirect()->route('authenticator.reset-password', ['token' => 'verified'])->with('status', 'OTP verified successfully!');
    }

    public function loginWithOtp(Request $request)
    {
        return redirect()->intended(config('authenticator.redirect_to', '/dashboard'));
    }
}
