<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\LogoutController;
use App\Http\Controllers\Api\V1\Auth\ProfileController;
use App\Http\Controllers\Api\V1\Auth\EmailVerificationController;
use App\Http\Controllers\Api\V1\BiometricController;
use App\Http\Controllers\Api\V1\HomeController;
use App\Http\Controllers\Api\V1\RateController;

// Public routes
Route::prefix('v1')->group(function () {
    // Authentication
    Route::post('register', [RegisterController::class, 'register']);
    Route::post('login', [LoginController::class, 'login']);
    
    // Email Verification (public)
    Route::post('verify-email', [EmailVerificationController::class, 'sendCode']);
    Route::post('confirm-email', [EmailVerificationController::class, 'confirmEmail']);
    Route::post('resend-verification', [EmailVerificationController::class, 'resendCode']);
    
    // Public rates
    Route::get('rates/crypto', [RateController::class, 'getCryptoRates']);
    Route::get('rates/gift-cards', [RateController::class, 'getGiftCardRates']);
    Route::get('rates', [RateController::class, 'getAllRates']);
});

// Protected routes (require authentication)
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('logout', [LogoutController::class, 'logout']);
    Route::get('profile', [ProfileController::class, 'show']);
    
    // Biometric Setup
    Route::post('enable-face-id', [BiometricController::class, 'updateFaceId']);
    Route::post('enable-fingerprint', [BiometricController::class, 'updateFingerprint']);
    Route::post('setup-biometrics', [BiometricController::class, 'setupBiometrics']);
    
    // Homepage
    Route::get('home', [HomeController::class, 'index']);
    
    // Rate Calculations
    Route::post('calculate/crypto', [RateController::class, 'calculateCrypto']);
    Route::post('calculate/gift-card', [RateController::class, 'calculateGiftCard']);
});
