<?php

namespace Ataurbdx\Authenticator\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Ataurbdx\Authenticator\Modules\Auth\Services\AuthService;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Quick check if an identifier exists in the database.
     */
    public function checkIdentifier(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identifier' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = $this->authService->findByIdentifier($request->identifier, $request->country_code);

        return response()->json([
            'success'      => true,
            'exists'       => !is_null($user),
            'user_name'    => $user ? ($user->name ?: ($user->first_name ?: 'User')) : null,
            'avatar'       => $user ? $user->avatar : null,
            'has_password' => $user ? $user->hasPassword() : false,
            'type'         => $this->authService->detectIdentifierType($request->identifier),
        ]);
    }

    /**
     * Universal Login (email / username / phone + password).
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identifier' => 'required|string',
            'password'   => 'required|string',
            'remember'   => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $result = $this->authService->attempt(
            $request->identifier,
            $request->password,
            $request->boolean('remember'),
            $request->country_code
        );

        if (!empty($result['requires_two_factor'])) {
            session(['auth_2fa_user_id' => $result['user_id']]);
        }

        $status = $result['success'] ? 200 : 401;
        return response()->json($result, $status);
    }

    /**
     * Universal Registration.
     */
    public function register(Request $request)
    {
        $userModel = config('auth.providers.users.model', 'App\\Models\\User');

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'last_name'  => 'nullable|string|max:100',
            'username'   => 'nullable|string|max:50|unique:' . (new $userModel)->getTable(),
            'email'      => 'nullable|email|max:255|unique:' . (new $userModel)->getTable(),
            'phone'      => 'nullable|string|max:30',
            'password'   => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // At least one contact point required
        if (empty($request->email) && empty($request->phone) && empty($request->username)) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide an email address or phone number.',
            ], 422);
        }

        $result = $this->authService->register($request->all());
        return response()->json($result, 201);
    }

    /**
     * Reset password with OTP code.
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identifier'   => 'required|string',
            'code'         => 'required|string',
            'password'     => 'required|string|min:6|confirmed',
            'country_code' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $result = $this->authService->resetPassword(
            $request->identifier,
            $request->code,
            $request->password,
            $request->country_code
        );

        $status = $result['success'] ? 200 : 422;
        if ($result['success']) {
            $result['redirect'] = url(config('authenticator.routes.web_prefix', 'auth') . '/login');
        }

        return response()->json($result, $status);
    }

    /**
     * Logout active session / token.
     */
    public function logout(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($request->user() && method_exists($request->user(), 'currentAccessToken') && $request->user()->currentAccessToken()) {
                $request->user()->currentAccessToken()->delete();
            }
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out.',
        ]);
    }
}
