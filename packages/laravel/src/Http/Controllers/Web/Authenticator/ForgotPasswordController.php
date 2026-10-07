<?php

namespace Ataurbdx\Authenticator\Http\Controllers\Web\Authenticator;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('authenticator::forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['identifier' => 'required']);
        return back()->with('status', 'We have sent your password reset instructions if the account exists.');
    }
}
