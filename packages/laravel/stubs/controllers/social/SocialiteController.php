<?php

namespace App\Http\Controllers\Authenticator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SocialiteController extends Controller
{
    public function redirect(string $provider)
    {
        // Dynamic Socialite redirect
        return redirect()->route('authenticator.account')->withErrors(['social' => 'Social provider not configured yet.']);
    }

    public function callback(string $provider)
    {
        // Dynamic Socialite callback
        return redirect()->intended(config('authenticator.redirect_to', '/dashboard'));
    }
}
