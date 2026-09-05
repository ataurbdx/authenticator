<?php

namespace Ataurbdx\Authenticator\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Ataurbdx\Authenticator\Modules\Pin\Services\PinService;

class PinController extends Controller
{
    protected PinService $pinService;

    public function __construct(PinService $pinService)
    {
        $this->pinService = $pinService;
    }

    /**
     * Set or update security PIN.
     */
    public function set(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pin' => 'required|string|min:4|max:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $this->pinService->setPin($user, $request->pin);

        return response()->json([
            'success' => true,
            'message' => 'Security PIN updated successfully.',
        ]);
    }

    /**
     * Verify PIN to unlock screen / sensitive action.
     */
    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pin' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $result = $this->pinService->verifyPin($user, $request->pin);
        $status = $result['success'] ? 200 : 422;

        return response()->json($result, $status);
    }

    /**
     * Manually lock screen.
     */
    public function lock(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $this->pinService->lockSession($user);
        }

        return response()->json([
            'success'    => true,
            'pin_locked' => true,
            'message'    => 'Screen locked.',
        ]);
    }
}
