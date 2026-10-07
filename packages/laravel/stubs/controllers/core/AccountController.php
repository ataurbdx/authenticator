<?php

namespace App\Http\Controllers\Authenticator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    protected $authService;

    public function __construct(\App\Services\AuthenticatorService $authService)
    {
        $this->authService = $authService;
    }

    public function showAccountForm()
    {
        return view('authenticator.account');
    }

    public function checkIdentifier(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'code'       => 'nullable|string',
        ]);

        $rawIdentifier = trim($request->input('identifier'));
        if ($request->filled('code') && preg_match('/^[0-9\s\-]+$/', $rawIdentifier)) {
            $rawIdentifier = trim($request->code) . ltrim($rawIdentifier, '0');
        }

        $result = $this->authService->checkUserExists($rawIdentifier);
        
        $user = $result['user'];
        $type = $result['type'];
        
        $redirectParams = ['type' => $type];
        if ($type === 'phone') {
            $redirectParams['code'] = $request->input('code', '+880');
            $redirectParams['number'] = ltrim($request->input('identifier'), '0');
        } else {
            $redirectParams[$type] = $request->input('identifier');
        }

        if ($user) {
            $name = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
            if (empty($name)) {
                $name = $user->name ?? $user->username ?? $user->email;
            }

            return response()->json([
                'status'  => 'success',
                'exists'  => true,
                'message' => "Account found for {$name}! Redirecting to sign in...",
                'data'    => [
                    'type'         => $type,
                    'name'         => $name,
                    'avatar'       => $user->avatar ?? null,
                    'redirect_url' => route('authenticator.sign-in', $redirectParams),
                ],
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'exists'  => false,
            'message' => 'No existing account found. Let\'s create one!',
            'data'    => [
                'type'         => $type,
                'redirect_url' => route('authenticator.sign-up', $redirectParams),
            ],
        ]);
    }
}
