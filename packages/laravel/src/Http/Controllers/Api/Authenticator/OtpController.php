<?php

namespace Ataurbdx\Authenticator\Http\Controllers\Api\Authenticator;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Ataurbdx\Authenticator\Modules\Otp\Services\OtpService;
use Ataurbdx\Authenticator\Modules\Authenticator\Services\AuthenticatorService;

class OtpController extends Controller
{
    protected OtpService $otpService;
    protected AuthenticatorService $authService;

    public function __construct(OtpService $otpService, AuthenticatorService $authService)
    {
        $this->otpService = $otpService;
        $this->authService = $authService;
    }

    /**
     * Send OTP code across any channel for any business purpose.
     */
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contact' => 'required|string',
            'channel' => 'nullable|string|in:email,phone,sms,whatsapp,telegram',
            'type'    => 'nullable|string|in:email,phone,sms,whatsapp,telegram',
            'purpose' => 'nullable|string|max:50',
            'action'  => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $contact = trim($request->contact);
        $channel = $request->channel ?? $request->type;
        $purpose = $request->purpose ?? $request->action ?? 'verification';

        // Normalize phone number if contact is a phone or channel is SMS/WhatsApp
        if (in_array(strtolower((string) $channel), ['phone', 'sms', 'whatsapp']) || $this->authService->detectIdentifierType($contact) === 'phone') {
            $contact = $this->authService->normalizePhone($contact, $request->country_code ?? '+880');
        }

        $result = $this->otpService->send(
            contact: $contact,
            purpose: $purpose,
            channel: $channel,
            userId: Auth::id(),
            metadata: $request->input('metadata', [])
        );

        return response()->json($result);
    }

    /**
     * Verify OTP code for any business purpose.
     */
    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contact' => 'required|string',
            'code'    => 'required|string',
            'purpose' => 'nullable|string|max:50',
            'action'  => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $contact = trim($request->contact);
        $purpose = $request->purpose ?? $request->action ?? 'verification';

        if ($this->authService->detectIdentifierType($contact) === 'phone') {
            $contact = $this->authService->normalizePhone($contact, $request->country_code ?? '+880');
        }

        $result = $this->otpService->verify(
            contact: $contact,
            code: $request->code,
            purpose: $purpose
        );

        $status = $result['success'] ? 200 : 422;
        return response()->json($result, $status);
    }

    /**
     * Verify Direct Token / Link via API.
     */
    public function verifyLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token'   => 'required|string',
            'purpose' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $result = $this->otpService->verifyByToken($request->token, $request->purpose);
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

        $contact = trim($request->contact);
        if ($this->authService->detectIdentifierType($contact) === 'phone') {
            $contact = $this->authService->normalizePhone($contact, $request->country_code ?? '+880');
        }

        // Verify code specifically for 'login' purpose
        $verifyResult = $this->otpService->verify($contact, $request->code, 'login');
        if (!$verifyResult['success']) {
            return response()->json($verifyResult, 422);
        }

        // Find user by verified contact
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
