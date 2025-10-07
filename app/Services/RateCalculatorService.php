<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class RateCalculatorService
{
    /**
     * Get all cryptocurrencies from PGold API
     */
    public function getCryptoRates()
    {
        return Cache::remember('crypto_rates', config('services.pgold.cache_ttl', 300), function () {
            try {
                $response = Http::timeout(10)
                    ->get(config('services.pgold.base_url') . '/api/guest/cryptocurrencies');

                if ($response->successful()) {
                    return $response->json('data', []);
                }

                Log::error('Failed to fetch crypto rates', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return [];
            } catch (\Exception $e) {
                Log::error('Error fetching crypto rates', [
                    'error' => $e->getMessage()
                ]);
                return [];
            }
        });
    }

    /**
     * Get all gift cards from PGold API
     */
    public function getGiftCardRates()
    {
        return Cache::remember('giftcard_rates', config('services.pgold.cache_ttl', 300), function () {
            try {
                $response = Http::timeout(10)
                    ->get(config('services.pgold.base_url') . '/api/guest/giftcards');

                if ($response->successful()) {
                    return $response->json('all_giftcards', []);
                }

                Log::error('Failed to fetch gift card rates', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return [];
            } catch (\Exception $e) {
                Log::error('Error fetching gift card rates', [
                    'error' => $e->getMessage()
                ]);
                return [];
            }
        });
    }

    /**
     * Get all rates (crypto + gift cards)
     */
    public function getAllRates()
    {
        return [
            'cryptocurrencies' => $this->getCryptoRates(),
            'gift_cards' => $this->getGiftCardRates(),
        ];
    }

    /**
     * Calculate crypto rate
     */
    public function calculateCrypto(string $code, string $action, ?float $amount = null, ?float $usdValue = null): array
    {
        $cryptos = $this->getCryptoRates();
        
        $crypto = collect($cryptos)->firstWhere('code', strtoupper($code));

        if (!$crypto) {
            throw ValidationException::withMessages([
                'code' => ['The selected cryptocurrency is not available.'],
            ]);
        }

        $buyRate = (float) ($crypto['buy_rate'] ?? 0);
        $sellRate = (float) ($crypto['sell_rate'] ?? 0);
        $usdRate = (float) ($crypto['usd_rate'] ?? 0);
        
        // Determine the rate based on action
        $appliedRate = match($action) {
            'buy' => $buyRate,
            'sell' => $sellRate,
            'swap' => ($buyRate + $sellRate) / 2, // Average for swap
            default => $buyRate,
        };

        // Calculate crypto amount and NGN value
        if ($usdValue) {
            // User provided USD value, calculate crypto amount
            $cryptoAmount = $usdRate > 0 ? $usdValue / $usdRate : 0;
            $ngnValue = $usdValue * $appliedRate;
        } else {
            // User provided crypto amount
            $cryptoAmount = $amount;
            $ngnValue = $cryptoAmount * $usdRate * $appliedRate;
        }

        return [
            'action' => $action,
            'asset' => [
                'id' => $crypto['id'],
                'code' => $crypto['code'],
                'name' => $crypto['name'],
                'type' => 'crypto',
                'icon' => $crypto['icon'] ?? null,
            ],
            'crypto_amount' => round($cryptoAmount, 8),
            'usd_value' => $usdValue ?? round($cryptoAmount * $usdRate, 2),
            'rate' => '₦' . number_format($appliedRate, 2),
            'buy_rate' => $buyRate,
            'sell_rate' => $sellRate,
            'applied_rate' => $appliedRate,
            'total_value' => 'NGN ' . number_format(round($ngnValue, 2), 2),
            'exchange_value_ngn' => round($ngnValue, 2),
            'currency' => 'NGN',
            'networks' => $crypto['networks'] ?? [],
            'disclaimer' => 'This is an estimated rate. Actual rate may differ',
            'calculated_at' => now()->toISOString(),
        ];
    }

    /**
     * Calculate gift card rate
     */
    public function calculateGiftCard(string $giftCardId, string $countryId, string $rangeId, string $categoryId, string $action, float $amount): array
    {
        $giftCards = $this->getGiftCardRates();
        
        $giftCard = collect($giftCards)->firstWhere('id', (int) $giftCardId);

        if (!$giftCard) {
            throw ValidationException::withMessages([
                'gift_card_id' => ['The selected gift card is not available.'],
            ]);
        }

        // Find the country
        $country = collect($giftCard['countries'] ?? [])->firstWhere('id', (int) $countryId);
        
        if (!$country) {
            throw ValidationException::withMessages([
                'country_id' => ['The selected country is not available for this gift card.'],
            ]);
        }

        // Find the range
        $range = collect($country['ranges'] ?? [])->firstWhere('id', (int) $rangeId);
        
        if (!$range) {
            throw ValidationException::withMessages([
                'range_id' => ['The selected range is not available.'],
            ]);
        }

        // Find the receipt category
        $category = collect($range['receipt_categories'] ?? [])->firstWhere('id', (int) $categoryId);
        
        if (!$category) {
            throw ValidationException::withMessages([
                'category_id' => ['The selected receipt category is not available.'],
            ]);
        }

        // Validate amount is within range
        if ($amount < (float) $range['min'] || $amount > (float) $range['max']) {
            throw ValidationException::withMessages([
                'amount' => ["Amount must be between {$range['min']} and {$range['max']} {$country['currency']['code']}."],
            ]);
        }

        // Base rate from category
        $baseRate = (float) $category['amount'];
        
        // Apply action-based adjustments
        $appliedRate = match($action) {
            'sell' => $baseRate,                    // Standard rate for selling
            'buy' => $baseRate * 1.05,              // 5% markup for buying
            'trade' => $baseRate * 0.98,            // 2% discount for trading
            default => $baseRate,
        };

        // Calculate NGN value
        $ngnValue = $amount * $appliedRate;

        return [
            'action' => $action,
            'gift_card' => [
                'id' => $giftCard['id'],
                'title' => $giftCard['title'],
                'image' => $giftCard['image'] ?? null,
            ],
            'country' => [
                'id' => $country['id'],
                'name' => $country['name'],
                'iso' => $country['iso'],
                'currency' => $country['currency'],
            ],
            'range' => [
                'id' => $range['id'],
                'min' => (float) $range['min'],
                'max' => (float) $range['max'],
            ],
            'category' => [
                'id' => $category['id'],
                'title' => $category['title'],
                'base_rate' => $baseRate,
                'applied_rate' => $appliedRate,
            ],
            'card_amount' => $amount,
            'card_currency' => $country['currency']['code'],
            'rate' => '₦' . number_format($appliedRate, 2),
            'total_value' => 'NGN ' . number_format(round($ngnValue, 2), 2),
            'exchange_value_ngn' => round($ngnValue, 2),
            'disclaimer' => 'This is an estimated rate. Actual rate may differ',
            'calculated_at' => now()->toISOString(),
        ];
    }

    /**
     * Find crypto by code
     */
    public function findCryptoByCode(string $code): ?array
    {
        $cryptos = $this->getCryptoRates();
        return collect($cryptos)->firstWhere('code', strtoupper($code));
    }

    /**
     * Find gift card by ID
     */
    public function findGiftCardById(int $id): ?array
    {
        $giftCards = $this->getGiftCardRates();
        return collect($giftCards)->firstWhere('id', $id);
    }
}
