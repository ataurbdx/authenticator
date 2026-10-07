<?php

namespace Ataurbdx\Authenticator\Http\Controllers\Web\Authenticator;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request, $token = null)
    {
        return view('authenticator::reset-password')->with(
            ['token' => $token, 'identifier' => $request->identifier]
        );
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token'      => 'required',
            'identifier' => 'required',
            'password'   => 'required|confirmed|min:8',
        ]);

        $userModel = config('auth.providers.users.model', 'App\\Models\\User');
        $user = $userModel::where('email', $request->identifier)
            ->orWhere('phone', $request->identifier)
            ->orWhere('username', $request->identifier)
            ->first();

        if ($user) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            return redirect()->route('authenticator.sign-in')->with('status', 'Password reset successfully. You may now sign in.');
        }

        return back()->withErrors(['identifier' => 'Account could not be found.']);
    }
}
