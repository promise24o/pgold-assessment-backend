<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\BiometricRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BiometricController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    /**
     * Enable/disable Face ID
     */
    public function updateFaceId(Request $request): JsonResponse
    {
        $request->validate([
            'enabled' => ['required', 'boolean'],
        ]);

        $user = $this->authService->updateBiometric(
            $request->user(),
            'face_id',
            $request->enabled
        );

        return response()->json([
            'message' => $request->enabled ? 'Face ID enabled successfully' : 'Face ID disabled successfully',
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Enable/disable Fingerprint
     */
    public function updateFingerprint(Request $request): JsonResponse
    {
        $request->validate([
            'enabled' => ['required', 'boolean'],
        ]);

        $user = $this->authService->updateBiometric(
            $request->user(),
            'fingerprint',
            $request->enabled
        );

        return response()->json([
            'message' => $request->enabled ? 'Fingerprint enabled successfully' : 'Fingerprint disabled successfully',
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Setup biometrics (generic endpoint)
     */
    public function setupBiometrics(BiometricRequest $request): JsonResponse
    {
        $user = $this->authService->updateBiometric(
            $request->user(),
            $request->type,
            $request->enabled
        );

        $biometricName = $request->type === 'face_id' ? 'Face ID' : 'Fingerprint';
        $action = $request->enabled ? 'enabled' : 'disabled';

        return response()->json([
            'message' => "{$biometricName} {$action} successfully",
            'user' => new UserResource($user),
        ]);
    }
}
