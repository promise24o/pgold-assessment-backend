# PGold Assessment Backend

A professional Laravel 12 REST API backend for a crypto and gift card trading mobile application.

## Features

✅ **Authentication & Onboarding**
- User registration with validation
- Email & password login
- Token-based authentication (Laravel Sanctum)
- Logout and profile retrieval
- Email verification with 6-digit codes
- Biometric setup (Face ID & Fingerprint)

✅ **Homepage API**
- Fetch available crypto rates
- Fetch available gift card rates
- Announcements and featured items
- Dashboard data for mobile app

✅ **Rate Calculator**
- Crypto rate calculation
- Gift card rate calculation
- Real-time exchange value computation

✅ **Professional Architecture**
- Form Request validation
- API Resources for responses
- Service layer for business logic
- Custom exception handling
- Rate limiting
- CORS configuration
- API versioning (v1)

## Tech Stack

- **Framework:** Laravel 12
- **Authentication:** Laravel Sanctum
- **Database:** SQLite (easily switchable to MySQL/PostgreSQL)
- **PHP Version:** 8.2+

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- SQLite (or MySQL/PostgreSQL)

### Setup Steps

1. **Clone the repository**
```bash
git clone <repository-url>
cd pgold-assessment-backend
```

2. **Install dependencies**
```bash
composer install
```

3. **Environment configuration**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure database**
The project uses SQLite by default. The database file will be created automatically.

For MySQL/PostgreSQL, update `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pgold
DB_USERNAME=root
DB_PASSWORD=
```

5. **Run migrations and seed database**
```bash
php artisan migrate:fresh --seed
```

This will create all tables and seed sample crypto/gift card rates.

6. **Start the development server**
```bash
php artisan serve
```

The API will be available at `http://localhost:8000`

## API Documentation

See [API_DOCUMENTATION.md](./API_DOCUMENTATION.md) for complete API reference.

### Quick Test

**Register a user:**
```bash
curl -X POST http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -d '{
    "username": "testuser",
    "email": "test@example.com",
    "full_name": "Test User",
    "password": "Password123",
    "password_confirmation": "Password123"
  }'
```

**Login:**
```bash
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "Password123"
  }'
```

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/V1/          # API v1 controllers
│   ├── Requests/            # Form request validation
│   ├── Resources/           # API response resources
│   └── Middleware/          # Custom middleware
├── Models/                  # Eloquent models
├── Services/                # Business logic layer
└── Exceptions/              # Exception handling

database/
├── migrations/              # Database migrations
└── seeders/                 # Database seeders

routes/
└── api.php                  # API routes
```

## Key Features Implementation

### Authentication Flow
1. User registers → Email verification code sent
2. User verifies email with 6-digit code
3. User receives authentication token
4. User can setup biometrics (Face ID/Fingerprint)
5. User can login and access protected endpoints

### Rate Calculation
- Real-time calculation based on current rates
- Supports both crypto and gift cards
- Returns exchange value in NGN

### Security
- Password hashing (bcrypt)
- Token-based authentication (Sanctum)
- Rate limiting (60 req/min authenticated, 30 req/min guest)
- CORS enabled
- Input validation on all endpoints
- Email verification before full access

## Available Endpoints

### Public Endpoints
- `POST /api/v1/register` - Register user
- `POST /api/v1/login` - Login user
- `POST /api/v1/verify-email` - Send verification code
- `POST /api/v1/confirm-email` - Verify email
- `GET /api/v1/rates/*` - Get rates

### Protected Endpoints (Require Authentication)
- `POST /api/v1/logout` - Logout
- `GET /api/v1/profile` - Get profile
- `POST /api/v1/enable-face-id` - Enable Face ID
- `POST /api/v1/enable-fingerprint` - Enable Fingerprint
- `GET /api/v1/home` - Get homepage data
- `POST /api/v1/calculate/*` - Calculate rates

## Testing

Run tests:
```bash
php artisan test
```

## Deployment

For production deployment:

1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false`
3. Configure proper database credentials
4. Setup email service (Mailgun, SES, etc.)
5. Configure CORS for your frontend domain
6. Enable HTTPS
7. Setup queue workers for background jobs

## Email Configuration

By default, verification codes are logged. To send actual emails:

1. Configure mail settings in `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@pgold.com
MAIL_FROM_NAME="PGold"
```

2. Uncomment email sending in `EmailVerificationService.php`

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
