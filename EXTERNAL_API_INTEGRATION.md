# External API Integration - PGold Sandbox

## Overview

The application now fetches cryptocurrency and gift card rates from the **PGold Sandbox API**.

---

## API Endpoints Used

### Base URL
```
https://sandbox.pgoldapp.com
```

### Endpoints
1. **GET** `/api/guest/cryptocurrencies` - Fetch all cryptocurrencies with rates
2. **GET** `/api/guest/giftcards` - Fetch all gift cards with rates

---

## Implementation Details

### Caching Strategy
- **Cache Duration:** 5 minutes (300 seconds)
- **Cache Keys:** 
  - `crypto_rates` - For cryptocurrency data
  - `giftcard_rates` - For gift card data
- **Purpose:** Reduce API calls and improve performance

### Error Handling
- Graceful fallback to empty arrays on API failure
- Comprehensive logging of errors
- Timeout protection (10 seconds)

---

## Updated Endpoints

### 1. Get Crypto Rates
**GET** `/api/v1/rates/crypto`

**Response:**
```json
{
  "message": "Crypto rates retrieved successfully",
  "data": [
    {
      "id": 9,
      "name": "Bitcoin",
      "code": "BTC",
      "icon": "https://...",
      "networks": [...],
      "buy_rate": "1620",
      "sell_rate": "1580",
      "usd_rate": "108908.0000000000"
    }
  ]
}
```

---

### 2. Get Gift Card Rates
**GET** `/api/v1/rates/gift-cards`

**Response:**
```json
{
  "message": "Gift card rates retrieved successfully",
  "all_giftcards": [
    {
      "id": 1,
      "title": "AMAZON",
      "image": "https://...",
      "countries": [
        {
          "id": 1,
          "name": "UNITED KINGDOM",
          "currency": {...},
          "ranges": [...]
        }
      ]
    }
  ]
}
```

---

### 3. Calculate Crypto Rate
**POST** `/api/v1/calculate/crypto`

**Request:**
```json
{
  "code": "BTC",
  "amount": 0.5
}
```

**Response:**
```json
{
  "message": "Crypto rate calculated successfully",
  "calculation": {
    "asset": {
      "id": 9,
      "code": "BTC",
      "name": "Bitcoin",
      "type": "crypto",
      "icon": "https://..."
    },
    "amount": 0.5,
    "usd_rate": 108908.0,
    "buy_rate": 1620.0,
    "sell_rate": 1580.0,
    "currency": "NGN",
    "exchange_value": 88255480.0,
    "networks": [...],
    "calculated_at": "2025-10-07T00:00:00.000000Z"
  }
}
```

**Calculation Formula:**
```
NGN Value = amount × usd_rate × buy_rate
```

---

### 4. Calculate Gift Card Rate
**POST** `/api/v1/calculate/gift-card`

**Request:**
```json
{
  "gift_card_id": 1,
  "country_id": 2,
  "range_id": 4,
  "category_id": 4,
  "amount": 75
}
```

**Response:**
```json
{
  "message": "Gift card rate calculated successfully",
  "calculation": {
    "gift_card": {
      "id": 1,
      "title": "AMAZON",
      "image": "https://..."
    },
    "country": {
      "id": 2,
      "name": "UNITED STATES",
      "iso": "US",
      "currency": {
        "code": "USD",
        "symbol": "$"
      }
    },
    "range": {
      "id": 4,
      "min": 50,
      "max": 99
    },
    "category": {
      "id": 4,
      "title": "CASH RECEIPT",
      "rate_per_unit": 900.0
    },
    "amount": 75,
    "currency": "USD",
    "exchange_value_ngn": 67500.0,
    "calculated_at": "2025-10-07T00:00:00.000000Z"
  }
}
```

**Calculation Formula:**
```
NGN Value = amount × rate_per_unit
```

**Validation:**
- Amount must be within the selected range (min-max)
- All IDs must exist in the fetched data

---

### 5. Homepage Data
**GET** `/api/v1/home`

**Response:**
```json
{
  "message": "Homepage data retrieved successfully",
  "data": {
    "cryptocurrencies": [...],
    "gift_cards": [...],
    "announcements": [...],
    "featured": {
      "cryptocurrencies": [...],
      "gift_cards": [...]
    },
    "stats": {
      "total_cryptocurrencies": 13,
      "total_gift_cards": 3,
      "active_users": 1250,
      "transactions_today": 89
    }
  }
}
```

---

## Code Changes

### RateCalculatorService.php
**Key Changes:**
- Removed database queries
- Added HTTP client calls to PGold API
- Implemented caching layer
- Added error handling and logging
- New methods:
  - `getCryptoRates()` - Fetch from external API
  - `getGiftCardRates()` - Fetch from external API
  - `calculateCrypto()` - Calculate using external rates
  - `calculateGiftCard()` - Calculate with complex validation
  - `findCryptoByCode()` - Helper method
  - `findGiftCardById()` - Helper method

### RateController.php
**Key Changes:**
- Updated validation for new parameters
- Changed response structure to match external API
- Gift card calculation now requires:
  - `gift_card_id`
  - `country_id`
  - `range_id`
  - `category_id`
  - `amount`

### HomeController.php
**Key Changes:**
- Returns live data from external API
- Added stats section
- Featured items based on actual data

---

## Benefits

### ✅ Real-Time Data
- Always up-to-date rates from PGold
- No manual database updates needed

### ✅ Performance
- 5-minute cache reduces API calls
- Fast response times for users

### ✅ Reliability
- Graceful error handling
- Fallback to empty arrays
- Comprehensive logging

### ✅ Scalability
- No database storage for rates
- Reduced server load
- Easy to update cache duration

---

## Testing

### Test Crypto Calculation
```bash
curl -X POST http://localhost:8000/api/v1/calculate/crypto \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "code": "BTC",
    "amount": 0.5
  }'
```

### Test Gift Card Calculation
```bash
curl -X POST http://localhost:8000/api/v1/calculate/gift-card \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "gift_card_id": 1,
    "country_id": 2,
    "range_id": 4,
    "category_id": 4,
    "amount": 75
  }'
```

### Test Get Rates
```bash
# Get crypto rates
curl http://localhost:8000/api/v1/rates/crypto

# Get gift card rates
curl http://localhost:8000/api/v1/rates/gift-cards
```

---

## Cache Management

### Clear Cache Manually
```bash
php artisan cache:forget crypto_rates
php artisan cache:forget giftcard_rates
```

### Clear All Cache
```bash
php artisan cache:clear
```

---

## Configuration

### Environment Variables

Add these to your `.env` file:

```env
# PGold API Configuration
PGOLD_API_BASE_URL=https://sandbox.pgoldapp.com
PGOLD_CACHE_TTL=300
```

### Adjust Cache Duration
Update `.env`:
```env
PGOLD_CACHE_TTL=600  # 10 minutes
```

### Change API Base URL
For production, update `.env`:
```env
PGOLD_API_BASE_URL=https://api.pgoldapp.com
```

### Configuration File
Settings are defined in `config/services.php`:
```php
'pgold' => [
    'base_url' => env('PGOLD_API_BASE_URL', 'https://sandbox.pgoldapp.com'),
    'cache_ttl' => env('PGOLD_CACHE_TTL', 300),
],
```

---

## Error Scenarios

### API Unavailable
- Returns empty array
- Logs error to `storage/logs/laravel.log`
- User sees: `[]` or empty data

### Invalid Parameters
- Returns validation error (422)
- Clear error messages

### Timeout
- 10-second timeout protection
- Logs timeout error
- Returns empty array

---

## Monitoring

### Check Logs
```bash
tail -f storage/logs/laravel.log
```

### Look for:
- `Failed to fetch crypto rates`
- `Failed to fetch gift card rates`
- `Error fetching crypto rates`
- `Error fetching gift card rates`

---

## Next Steps

### Optional Enhancements
1. **Add retry logic** for failed API calls
2. **Implement fallback** to local database if API fails
3. **Add rate limiting** to prevent abuse
4. **Create admin panel** to view API status
5. **Add webhooks** for rate updates
6. **Implement real-time** updates via WebSockets

---

## Summary

✅ **External API Integration Complete**
✅ **Caching Implemented**
✅ **Error Handling Added**
✅ **All Endpoints Updated**
✅ **Documentation Complete**

The application now fetches live rates from PGold Sandbox API with proper caching, error handling, and performance optimization!
