# PGold Assessment Backend - API Documentation

## Base URL
```
http://localhost:8000/api/v1
```

## 🔗 External API Integration
This backend integrates with the **PGold Sandbox API** to fetch real-time cryptocurrency and gift card rates:
- **Crypto Rates:** `https://sandbox.pgoldapp.com/api/guest/cryptocurrencies`
- **Gift Card Rates:** `https://sandbox.pgoldapp.com/api/guest/giftcards`
- **Caching:** 5-minute cache for optimal performance
- **See:** [EXTERNAL_API_INTEGRATION.md](./EXTERNAL_API_INTEGRATION.md) for details

## Authentication
Most endpoints require authentication using Laravel Sanctum. Include the token in the Authorization header:
```
Authorization: Bearer {token}
```

---

## Authentication Endpoints

### 1. Register User
**POST** `/register`

Register a new user account.

**Request Body:**
```json
{
  "username": "johndoe",
  "email": "john@example.com",
  "full_name": "John Doe",
  "phone_number": "08012345678",
  "referral_code": "REF123",
  "heard_from": "Facebook",
  "password": "Password123",
  "password_confirmation": "Password123"
}
```

**Response (201):**
```json
{
  "message": "User registered successfully. Please verify your email.",
  "user": {
    "id": 1,
    "username": "johndoe",
    "email": "john@example.com",
    "full_name": "John Doe",
    "phone_number": "08012345678",
    "email_verified": false,
    "face_id_enabled": false,
    "fingerprint_enabled": false
  },
  "verification_code": "123456"
}
```

---

### 2. Login
**POST** `/login`

Authenticate user and receive access token.

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "Password123"
}
```

**Response (200):**
```json
{
  "message": "Login successful",
  "user": {
    "id": 1,
    "username": "johndoe",
    "email": "john@example.com",
    "full_name": "John Doe"
  },
  "token": "1|abcdef123456..."
}
```

---

### 3. Logout
**POST** `/logout`

🔒 **Requires Authentication**

Logout user and revoke all tokens.

**Response (200):**
```json
{
  "message": "Logged out successfully"
}
```

---

### 4. Get Profile
**GET** `/profile`

🔒 **Requires Authentication**

Get authenticated user's profile.

**Response (200):**
```json
{
  "user": {
    "id": 1,
    "username": "johndoe",
    "email": "john@example.com",
    "full_name": "John Doe",
    "phone_number": "08012345678",
    "email_verified": true,
    "face_id_enabled": false,
    "fingerprint_enabled": true
  }
}
```

---

## Email Verification Endpoints

### 5. Send Verification Code
**POST** `/verify-email`

Send a 6-digit verification code to user's email.

**Request Body:**
```json
{
  "email": "john@example.com"
}
```

**Response (200):**
```json
{
  "message": "Verification code sent to your email",
  "verification_code": "123456"
}
```

---

### 6. Confirm Email
**POST** `/confirm-email`

Verify email with the 6-digit code.

**Request Body:**
```json
{
  "email": "john@example.com",
  "code": "123456"
}
```

**Response (200):**
```json
{
  "message": "Email verified successfully",
  "user": {
    "id": 1,
    "email_verified": true
  },
  "token": "1|abcdef123456..."
}
```

**Error Response (422):**
```json
{
  "message": "Validation failed",
  "errors": {
    "code": ["The verification code is invalid or has expired."]
  }
}
```

---

### 7. Resend Verification Code
**POST** `/resend-verification`

Resend verification code to email.

**Request Body:**
```json
{
  "email": "john@example.com"
}
```

**Response (200):**
```json
{
  "message": "Verification code resent to your email",
  "verification_code": "654321"
}
```

---

## Biometric Setup Endpoints

### 8. Enable/Disable Face ID
**POST** `/enable-face-id`

🔒 **Requires Authentication**

Enable or disable Face ID for the authenticated user.

**Request Body:**
```json
{
  "enabled": true
}
```

**Response (200):**
```json
{
  "message": "Face ID enabled successfully",
  "user": {
    "id": 1,
    "face_id_enabled": true
  }
}
```

---

### 9. Enable/Disable Fingerprint
**POST** `/enable-fingerprint`

🔒 **Requires Authentication**

Enable or disable fingerprint authentication.

**Request Body:**
```json
{
  "enabled": true
}
```

**Response (200):**
```json
{
  "message": "Fingerprint enabled successfully",
  "user": {
    "id": 1,
    "fingerprint_enabled": true
  }
}
```

---

### 10. Setup Biometrics (Generic)
**POST** `/setup-biometrics`

🔒 **Requires Authentication**

Generic endpoint to setup any biometric type.

**Request Body:**
```json
{
  "type": "face_id",
  "enabled": true
}
```

**Valid types:** `face_id`, `fingerprint`

**Response (200):**
```json
{
  "message": "Face ID enabled successfully",
  "user": {
    "id": 1,
    "face_id_enabled": true,
    "fingerprint_enabled": false
  }
}
```

---

## Homepage Endpoint

### 11. Get Homepage Data
**GET** `/home`

🔒 **Requires Authentication**

Get homepage data including rates, announcements, and featured items.

**Response (200):**
```json
{
  "message": "Homepage data retrieved successfully",
  "data": {
    "crypto_rates": [
      {
        "id": 1,
        "type": "crypto",
        "name": "Bitcoin",
        "code": "BTC",
        "rate": 45000000,
        "currency": "NGN",
        "is_active": true
      }
    ],
    "gift_card_rates": [
      {
        "id": 6,
        "type": "gift_card",
        "name": "Amazon Gift Card (USD)",
        "code": "AMAZON_USD",
        "rate": 1400,
        "currency": "NGN",
        "is_active": true
      }
    ],
    "announcements": [
      {
        "id": 1,
        "title": "Welcome to PGold",
        "message": "Trade crypto and gift cards at the best rates",
        "type": "info"
      }
    ],
    "featured": {
      "crypto": [],
      "gift_cards": []
    }
  }
}
```

---

## Rate Endpoints

### 12. Get All Crypto Rates
**GET** `/rates/crypto`

Get all active cryptocurrency rates.

**Response (200):**
```json
{
  "message": "Crypto rates retrieved successfully",
  "rates": [
    {
      "id": 1,
      "type": "crypto",
      "name": "Bitcoin",
      "code": "BTC",
      "rate": 45000000,
      "currency": "NGN",
      "is_active": true
    },
    {
      "id": 2,
      "type": "crypto",
      "name": "Ethereum",
      "code": "ETH",
      "rate": 2500000,
      "currency": "NGN",
      "is_active": true
    }
  ]
}
```

---

### 13. Get All Gift Card Rates
**GET** `/rates/gift-cards`

Get all active gift card rates.

**Response (200):**
```json
{
  "message": "Gift card rates retrieved successfully",
  "rates": [
    {
      "id": 6,
      "type": "gift_card",
      "name": "Amazon Gift Card (USD)",
      "code": "AMAZON_USD",
      "rate": 1400,
      "currency": "NGN",
      "is_active": true
    }
  ]
}
```

---

### 14. Get All Rates
**GET** `/rates`

Get all active rates (crypto + gift cards).

**Response (200):**
```json
{
  "message": "All rates retrieved successfully",
  "rates": []
}
```

---

### 15. Calculate Crypto Rate
**POST** `/calculate/crypto`

🔒 **Requires Authentication**

Calculate exchange value for cryptocurrency using live rates from PGold API.

**Request Body:**
```json
{
  "code": "BTC",
  "amount": 0.5
}
```

**Response (200):**
```json
{
  "message": "Crypto rate calculated successfully",
  "calculation": {
    "asset": {
      "id": 9,
      "code": "BTC",
      "name": "Bitcoin",
      "type": "crypto",
      "icon": "https://res.cloudinary.com/dcgqnouof/image/upload/v1736485543/BTC_jhqdbu.png"
    },
    "amount": 0.5,
    "usd_rate": 108908.0,
    "buy_rate": 1620.0,
    "sell_rate": 1580.0,
    "currency": "NGN",
    "exchange_value": 88255480.0,
    "networks": [
      {
        "id": 23,
        "name": "Bitcoin",
        "code": "BTC",
        "fee": "0.0003000000",
        "minimum": "0.0005000000"
      }
    ],
    "calculated_at": "2025-10-07T00:00:00.000000Z"
  }
}
```

**Calculation Formula:**
```
NGN Value = amount × usd_rate × buy_rate
Example: 0.5 × 108908 × 1620 = 88,255,480 NGN
```

---

### 16. Calculate Gift Card Rate
**POST** `/calculate/gift-card`

🔒 **Requires Authentication**

Calculate exchange value for gift cards using live rates from PGold API.

**Request Body:**
```json
{
  "gift_card_id": 1,
  "country_id": 2,
  "range_id": 4,
  "category_id": 4,
  "amount": 75
}
```

**Parameters:**
- `gift_card_id` - ID of the gift card (e.g., 1 for Amazon)
- `country_id` - ID of the country (e.g., 2 for United States)
- `range_id` - ID of the amount range (e.g., 4 for $50-$99)
- `category_id` - ID of the receipt category (e.g., 4 for Cash Receipt)
- `amount` - Amount in the gift card's currency

**Response (200):**
```json
{
  "message": "Gift card rate calculated successfully",
  "calculation": {
    "gift_card": {
      "id": 1,
      "title": "AMAZON",
      "image": "https://sandbox.pgoldapp.com/uploads/images/giftcards//1725448322.png"
    },
    "country": {
      "id": 2,
      "name": "UNITED STATES",
      "iso": "US",
      "currency": {
        "id": 1,
        "name": "US Dollar",
        "symbol": "$",
        "code": "USD"
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
Example: 75 × 900 = 67,500 NGN
```

**How to Get IDs:**
1. First, call `GET /api/v1/rates/gift-cards` to see all gift cards
2. Select a gift card and note its `id`
3. Choose a country from the gift card's `countries` array
4. Select a range from the country's `ranges` array
5. Pick a category from the range's `receipt_categories` array
6. Ensure your amount is between `range.min` and `range.max`

---

## Error Responses

### Validation Error (422)
```json
{
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password field is required."]
  }
}
```

### Unauthenticated (401)
```json
{
  "message": "Unauthenticated"
}
```

### Not Found (404)
```json
{
  "message": "Resource not found"
}
```

### Server Error (500)
```json
{
  "message": "An error occurred"
}
```

---

## Rate Limiting

API endpoints are rate-limited to prevent abuse:
- **60 requests per minute** for authenticated users
- **30 requests per minute** for guest users

---

## Available Asset Codes

### Cryptocurrencies
- `BTC` - Bitcoin
- `ETH` - Ethereum
- `USDT` - Tether
- `BNB` - Binance Coin
- `ADA` - Cardano

### Gift Cards
- `AMAZON_USD` - Amazon Gift Card (USD)
- `ITUNES_USD` - iTunes Gift Card (USD)
- `GOOGLEPLAY_USD` - Google Play Gift Card (USD)
- `STEAM_USD` - Steam Gift Card (USD)
- `WALMART_USD` - Walmart Gift Card (USD)
- `EBAY_USD` - eBay Gift Card (USD)

---

## Testing the API

### Using cURL

**Register:**
```bash
curl -X POST http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -d '{
    "username": "johndoe",
    "email": "john@example.com",
    "full_name": "John Doe",
    "password": "Password123",
    "password_confirmation": "Password123"
  }'
```

**Login:**
```bash
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "Password123"
  }'
```

**Get Profile (with token):**
```bash
curl -X GET http://localhost:8000/api/v1/profile \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

---

## Notes

1. **Email Verification Codes** are valid for 10 minutes
2. **Verification codes** are logged in the application log (check `storage/logs/laravel.log`)
3. In **debug mode**, verification codes are returned in the API response
4. **Tokens** are revoked on logout
5. All **timestamps** are in ISO 8601 format
6. **CORS** is enabled for all origins (configure in production)
