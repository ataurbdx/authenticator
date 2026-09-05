<?php

namespace Ataurbdx\Authenticator\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Ataurbdx\Authenticator\Modules\Otp\Services\OtpService;
use Ataurbdx\Authenticator\Modules\Auth\Services\AuthService;

class OtpController extends Controller
{
    protected OtpService $otpService;
    protected AuthService $authService;

    public function __construct(OtpService $otpService, AuthService $authService)
    {
        $this->otpService = $otpService;
        $this->authService = $authService;
    }

    /**
     * Send OTP code.
     */
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type'    => 'required|in:email,phone',
            'contact' => 'required|string',
            'action'  => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $contact = $request->contact;
        if ($request->type === 'phone') {
            $contact = $this->authService->normalizePhone($contact, $request->country_code ?? '+880');
        }

        $result = $this->otpService->sendOtp(
            $request->type,
            $contact,
            $request->action ?? 'verify',
            Auth::id()
        );

        return response()->json($result);
    }

    /**
     * Verify OTP code.
     */
    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contact' => 'required|string',
            'code'    => 'required|string',
            'action'  => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $contact = $request->contact;
        if ($this->authService->detectIdentifierType($contact) === 'phone') {
            $contact = $this->authService->normalizePhone($contact, $request->country_code ?? '+880');
        }

        $result = $this->otpService->verifyOtp($contact, $request->code, $request->action ?? 'verify');
        $status = $result['success'] ? 200 : 422;

        return response()->json($result, $status);
    }

    /**
     * Passwordless Login using OTP.
     */
    public function loginWithOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contact' => 'required|string',
            'code'    => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $contact = $request->contact;
        if ($this->authService->detectIdentifierType($contact) === 'phone') {
            $contact = $this->authService->normalizePhone($contact, $request->country_code ?? '+880');
        }

        // Verify code
        $verifyResult = $this->otpService->verifyOtp($contact, $request->code, 'login');
        if (!$verifyResult['success']) {
            return response()->json($verifyResult, 422);
        }

        // Find user
        $user = $this->authService->findByIdentifier($contact);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No account associated with this verified contact.',
            ], 404);
        }

        Auth::login($user, true);

        $token = null;
        if (method_exists($user, 'createToken')) {
            $token = $user->createToken('auth_token')->plainTextToken;
        }

        return response()->json([
            'success' => true,
            'message' => 'Passwordless login successful.',
            'user'    => $user,
            'token'   => $token,
        ]);
    }
}
