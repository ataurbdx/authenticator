<?php

namespace Ataurbdx\Authenticator\Http\Controllers\Web\Authenticator;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class TwoFactorController extends Controller
{
    public function showChallenge()
    {
        $view = view()->exists('authenticator.two-factor.challenge')
            ? 'authenticator.two-factor.challenge'
            : (view()->exists('authenticator::two-factor.challenge')
                ? 'authenticator::two-factor.challenge'
                : 'authenticator::tailwind.two-factor.challenge');

        return view($view);
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => 'required']);
        return redirect()->intended(config('authenticator.redirect_to', '/dashboard'));
    }
}
