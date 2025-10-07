<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\RateCalculatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RateController extends Controller
{
    public function __construct(
        private RateCalculatorService $rateService
    ) {}

    /**
     * Calculate crypto rate
     */
    public function calculateCrypto(Request $request): JsonResponse
    {
        $request->validate([
            'code' => ['required', 'string'],
            'action' => ['required', 'string', 'in:buy,sell,swap'],
            'amount' => ['nullable', 'numeric', 'min:0.00000001'],
            'usd_value' => ['nullable', 'numeric', 'min:0.01'],
        ]);

        // Ensure at least one of amount or usd_value is provided
        if (!$request->amount && !$request->usd_value) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => [
                    'amount' => ['Either amount or usd_value must be provided.']
                ]
            ], 422);
        }

        $result = $this->rateService->calculateCrypto(
            $request->code,
            $request->action,
            $request->amount,
            $request->usd_value
        );

        return response()->json([
            'message' => 'Crypto rate calculated successfully',
            'estimated_rate' => true,
            'data' => $result,
        ]);
    }

    /**
     * Calculate gift card rate
     */
    public function calculateGiftCard(Request $request): JsonResponse
    {
        $request->validate([
            'gift_card_id' => ['required', 'integer'],
            'country_id' => ['required', 'integer'],
            'range_id' => ['required', 'integer'],
            'category_id' => ['required', 'integer'],
            'action' => ['required', 'string', 'in:sell,buy,trade'],
            'amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        $result = $this->rateService->calculateGiftCard(
            $request->gift_card_id,
            $request->country_id,
            $request->range_id,
            $request->category_id,
            $request->action,
            $request->amount
        );

        return response()->json([
            'message' => 'Gift card rate calculated successfully',
            'estimated_rate' => true,
            'data' => $result,
        ]);
    }

    /**
     * Get all crypto rates
     */
    public function getCryptoRates(): JsonResponse
    {
        $rates = $this->rateService->getCryptoRates();

        return response()->json([
            'message' => 'Crypto rates retrieved successfully',
            'data' => $rates,
        ]);
    }

    /**
     * Get all gift card rates
     */
    public function getGiftCardRates(): JsonResponse
    {
        $rates = $this->rateService->getGiftCardRates();

        return response()->json([
            'message' => 'Gift card rates retrieved successfully',
            'all_giftcards' => $rates,
        ]);
    }

    /**
     * Get all rates
     */
    public function getAllRates(): JsonResponse
    {
        $rates = $this->rateService->getAllRates();

        return response()->json([
            'message' => 'All rates retrieved successfully',
            'data' => $rates,
        ]);
    }
}
