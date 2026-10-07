<?php

namespace App\Http\Controllers\Authenticator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PinController extends Controller
{
    public function showSetForm()
    {
        return view('authenticator.pin.set');
    }

    public function setPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|min:4|max:6|confirmed',
        ]);

        return back()->with('status', 'Security PIN saved successfully.');
    }
}
