<?php

namespace App\Http\Controllers\Authenticator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TwoFactorController extends Controller
{
    public function showChallenge()
    {
        return view('authenticator.two-factor.challenge');
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => 'required']);
        return redirect()->intended(config('authenticator.redirect_to', '/dashboard'));
    }
}
