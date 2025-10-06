<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ConfirmEmailRequest;
use App\Http\Requests\Auth\VerifyEmailRequest;
use App\Http\Resources\UserResource;
use App\Services\EmailVerificationService;
use Illuminate\Http\JsonResponse;

class EmailVerificationController extends Controller
{
    public function __construct(
        private EmailVerificationService $emailService
    ) {}

    /**
     * Send verification code to email
     */
    public function sendCode(VerifyEmailRequest $request): JsonResponse
    {
        $code = $this->emailService->sendVerificationCode($request->email);

        return response()->json([
            'message' => 'Verification code sent to your email',
            'verification_code' => config('app.debug') ? $code : null, // Only in debug mode
        ]);
    }

    /**
     * Verify email with code
     */
    public function confirmEmail(ConfirmEmailRequest $request): JsonResponse
    {
        $user = $this->emailService->verifyCode(
            $request->email,
            $request->code
        );

        // Generate token after verification
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Email verified successfully',
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }

    /**
     * Resend verification code
     */
    public function resendCode(VerifyEmailRequest $request): JsonResponse
    {
        $code = $this->emailService->resendVerificationCode($request->email);

        return response()->json([
            'message' => 'Verification code resent to your email',
            'verification_code' => config('app.debug') ? $code : null, // Only in debug mode
        ]);
    }
}
