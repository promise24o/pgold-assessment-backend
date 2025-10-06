<?php

namespace Database\Seeders;

use App\Models\Rate;
use Illuminate\Database\Seeder;

class RateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rates = [
            // Crypto rates
            [
                'type' => 'crypto',
                'name' => 'Bitcoin',
                'code' => 'BTC',
                'rate' => 45000000.00, // 1 BTC = 45M NGN
                'currency' => 'NGN',
                'is_active' => true,
            ],
            [
                'type' => 'crypto',
                'name' => 'Ethereum',
                'code' => 'ETH',
                'rate' => 2500000.00, // 1 ETH = 2.5M NGN
                'currency' => 'NGN',
                'is_active' => true,
            ],
            [
                'type' => 'crypto',
                'name' => 'USDT',
                'code' => 'USDT',
                'rate' => 1550.00, // 1 USDT = 1550 NGN
                'currency' => 'NGN',
                'is_active' => true,
            ],
            [
                'type' => 'crypto',
                'name' => 'Binance Coin',
                'code' => 'BNB',
                'rate' => 450000.00, // 1 BNB = 450K NGN
                'currency' => 'NGN',
                'is_active' => true,
            ],
            [
                'type' => 'crypto',
                'name' => 'Cardano',
                'code' => 'ADA',
                'rate' => 800.00, // 1 ADA = 800 NGN
                'currency' => 'NGN',
                'is_active' => true,
            ],

            // Gift card rates
            [
                'type' => 'gift_card',
                'name' => 'Amazon Gift Card (USD)',
                'code' => 'AMAZON_USD',
                'rate' => 1400.00, // $1 = 1400 NGN
                'currency' => 'NGN',
                'is_active' => true,
            ],
            [
                'type' => 'gift_card',
                'name' => 'iTunes Gift Card (USD)',
                'code' => 'ITUNES_USD',
                'rate' => 1350.00, // $1 = 1350 NGN
                'currency' => 'NGN',
                'is_active' => true,
            ],
            [
                'type' => 'gift_card',
                'name' => 'Google Play Gift Card (USD)',
                'code' => 'GOOGLEPLAY_USD',
                'rate' => 1300.00, // $1 = 1300 NGN
                'currency' => 'NGN',
                'is_active' => true,
            ],
            [
                'type' => 'gift_card',
                'name' => 'Steam Gift Card (USD)',
                'code' => 'STEAM_USD',
                'rate' => 1250.00, // $1 = 1250 NGN
                'currency' => 'NGN',
                'is_active' => true,
            ],
            [
                'type' => 'gift_card',
                'name' => 'Walmart Gift Card (USD)',
                'code' => 'WALMART_USD',
                'rate' => 1200.00, // $1 = 1200 NGN
                'currency' => 'NGN',
                'is_active' => true,
            ],
            [
                'type' => 'gift_card',
                'name' => 'eBay Gift Card (USD)',
                'code' => 'EBAY_USD',
                'rate' => 1150.00, // $1 = 1150 NGN
                'currency' => 'NGN',
                'is_active' => true,
            ],
        ];

        foreach ($rates as $rate) {
            Rate::updateOrCreate(
                ['code' => $rate['code']],
                $rate
            );
        }
    }
}
