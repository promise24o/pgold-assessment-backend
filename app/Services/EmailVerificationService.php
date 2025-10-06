<?php

namespace App\Services;

use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class EmailVerificationService
{
    /**
     * Generate and send verification code
     */
    public function sendVerificationCode(string $email): string
    {
        // Delete old codes for this email
        VerificationCode::where('email', $email)->delete();

        // Generate 6-digit code
        $code = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        // Store code with 10 minute expiration
        VerificationCode::create([
            'email' => $email,
            'code' => $code,
            'expires_at' => now()->addMinutes(10),
        ]);

        // Send email (for now, just log it - you can configure mail later)
        Log::info("Verification code for {$email}: {$code}");

        // In production, send actual email:
        // Mail::to($email)->send(new VerificationCodeMail($code));

        return $code; // Return for development/testing purposes
    }

    /**
     * Verify the code and mark email as verified
     */
    public function verifyCode(string $email, string $code): User
    {
        $verificationCode = VerificationCode::where('email', $email)
            ->where('code', $code)
            ->valid()
            ->first();

        if (!$verificationCode) {
            throw ValidationException::withMessages([
                'code' => ['The verification code is invalid or has expired.'],
            ]);
        }

        // Find user and mark as verified
        $user = User::where('email', $email)->firstOrFail();
        $user->markEmailAsVerified();

        // Delete used code
        $verificationCode->delete();

        return $user;
    }

    /**
     * Resend verification code
     */
    public function resendVerificationCode(string $email): string
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['User not found.'],
            ]);
        }

        if ($user->hasVerifiedEmail()) {
            throw ValidationException::withMessages([
                'email' => ['Email is already verified.'],
            ]);
        }

        return $this->sendVerificationCode($email);
    }
}
