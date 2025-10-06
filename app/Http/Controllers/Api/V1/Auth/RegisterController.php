<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use App\Services\EmailVerificationService;
use Illuminate\Http\JsonResponse;

class RegisterController extends Controller
{
    public function __construct(
        private AuthService $authService,
        private EmailVerificationService $emailService
    ) {}

    /**
     * Register a new user
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->authService->register($request->validated());

        // Send verification code
        $code = $this->emailService->sendVerificationCode($user->email);

        return response()->json([
            'message' => 'User registered successfully. Please verify your email.',
            'user' => new UserResource($user),
            'verification_code' => config('app.debug') ? $code : null, // Only in debug mode
        ], 201);
    }
}
