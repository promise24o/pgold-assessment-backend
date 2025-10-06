<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Register a new user
     */
    public function register(array $data): User
    {
        return User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'full_name' => $data['full_name'],
            'phone_number' => $data['phone_number'] ?? null,
            'referral_code' => $data['referral_code'] ?? null,
            'heard_from' => $data['heard_from'] ?? null,
            'password' => $data['password'], // Auto-hashed by model cast
        ]);
    }

    /**
     * Authenticate user and return token
     */
    public function login(string $email, string $password): array
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Revoke all previous tokens
        $user->tokens()->delete();

        // Create new token
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Logout user by revoking all tokens
     */
    public function logout(User $user): void
    {
        $user->tokens()->delete();
    }

    /**
     * Update biometric settings
     */
    public function updateBiometric(User $user, string $type, bool $enabled): User
    {
        $field = $type === 'face_id' ? 'face_id_enabled' : 'fingerprint_enabled';
        
        $user->update([
            $field => $enabled,
        ]);

        return $user->fresh();
    }
}
