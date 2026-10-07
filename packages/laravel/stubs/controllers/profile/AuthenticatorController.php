<?php

namespace App\Http\Controllers\Authenticator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthenticatorController extends Controller
{
    public function dashboard()
    {
        return view('authenticator.profile.dashboard');
    }

    public function profile()
    {
        return view('authenticator.profile.profile');
    }

    public function updateUsername(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'username' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9_\-]+$/',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
        ], [
            'username.regex' => 'Username can only contain letters, numbers, hyphens, and underscores.',
        ]);

        $user->update([
            'username' => $request->username,
        ]);

        return back()->with('status', 'Username updated successfully.');
    }

    public function updateEmail(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ]);

        $emailChanged = strtolower(trim($user->email ?? '')) !== strtolower(trim($request->email));

        $user->update([
            'email'             => $request->email,
            'email_verified_at' => $emailChanged ? null : $user->email_verified_at,
        ]);

        $msg = $emailChanged 
            ? 'Email address updated. Please verify your new email address.' 
            : 'Email address updated successfully.';

        return back()->with('status', $msg);
    }

    public function updatePhone(Request $request)
    {
        $user = auth()->user();

        // Prepare phone from code and number before validation (same as SignupController)
        if ($request->filled('number')) {
            $cc = trim($request->input('code', '+880'));
            $num = ltrim($request->input('number'), '0');
            $request->merge([
                'phone' => $cc . $num
            ]);
        }

        $request->validate([
            'code'   => 'nullable|string|max:10',
            'number' => 'required|numeric|digits:10',
            'phone'  => [
                'required',
                'string',
                Rule::unique('users', 'phone')->ignore($user->id),
            ],
        ], [
            'number.required' => 'Phone number is required.',
            'number.digits'   => 'Phone number must be exactly 10 digits.',
            'phone.unique'    => 'This phone number is already registered to another account.',
        ]);

        $formattedPhone = $request->input('phone');
        $phoneChanged = ($user->phone ?? '') !== $formattedPhone;

        $phoneData = [
            'code'   => trim($request->input('code', '+880')),
            'number' => ltrim($request->input('number'), '0'),
        ];

        $user->update([
            'phone'             => $formattedPhone,
            'phone_data'        => json_encode($phoneData),
            'phone_verified_at' => $phoneChanged ? null : $user->phone_verified_at,
        ]);

        $msg = $phoneChanged 
            ? 'Phone number updated. Please verify your new phone number.' 
            : 'Phone number updated successfully.';

        return back()->with('status', $msg);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'first_name' => 'nullable|string|max:100',
            'last_name'  => 'nullable|string|max:100',
            'about'      => 'nullable|string|max:2000',
            'avatar'     => 'nullable|image|max:2048',
        ]);

        $data = [
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'about'      => $request->about,
        ];

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = asset('storage/' . $path);
        }

        $user->update($data);

        return back()->with('status', 'Profile details updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $rules = [
            'password' => 'required|string|min:8|confirmed',
        ];

        if (!empty($user->password)) {
            $rules['current_password'] = 'required|current_password';
        }

        $request->validate($rules);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('status', 'Password updated successfully.');
    }
}
