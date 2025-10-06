<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\RateCalculatorService;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    public function __construct(
        private RateCalculatorService $rateService
    ) {}

    /**
     * Get homepage data
     */
    public function index(): JsonResponse
    {
        $cryptoRates = $this->rateService->getCryptoRates();
        $giftCardRates = $this->rateService->getGiftCardRates();

        return response()->json([
            'message' => 'Homepage data retrieved successfully',
            'data' => [
                'cryptocurrencies' => collect($cryptoRates)->take(5)->values(),
                'gift_cards' => collect($giftCardRates)->take(5)->values(),
                'announcements' => [
                    [
                        'id' => 1,
                        'title' => 'Welcome to PGold',
                        'message' => 'Trade crypto and gift cards at the best rates',
                        'type' => 'info',
                        'created_at' => now()->toISOString(),
                    ],
                    [
                        'id' => 2,
                        'title' => 'New Rates Available',
                        'message' => 'Check out our updated exchange rates',
                        'type' => 'success',
                        'created_at' => now()->subHours(2)->toISOString(),
                    ],
                ],
                'featured' => [
                    'cryptocurrencies' => collect($cryptoRates)->where('is_stable', 0)->take(3)->values(),
                    'gift_cards' => collect($giftCardRates)->take(3)->values(),
                ],
                'stats' => [
                    'total_cryptocurrencies' => count($cryptoRates),
                    'total_gift_cards' => count($giftCardRates),
                    'active_users' => 1250,
                    'transactions_today' => 89,
                ],
            ],
        ]);
    }
}
