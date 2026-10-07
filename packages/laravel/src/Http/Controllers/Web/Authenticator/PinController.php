<?php

namespace Ataurbdx\Authenticator\Http\Controllers\Web\Authenticator;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class PinController extends Controller
{
    public function showSetForm()
    {
        $view = view()->exists('authenticator.pin.set')
            ? 'authenticator.pin.set'
            : (view()->exists('authenticator::pin.set')
                ? 'authenticator::pin.set'
                : 'authenticator::tailwind.pin.set');

        return view($view);
    }

    public function setPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|min:4|max:6|confirmed',
        ]);

        return back()->with('status', 'Security PIN saved successfully.');
    }
}
