# PPOB Game API

API untuk layanan PPOB (Payment Point Online Bank) khusus game online. API ini mendukung multiple provider seperti ApiGames, Codashop, dan DuniaGames untuk melakukan top-up berbagai game populer.

## Fitur Utama

- **Multi-Provider Support**: Mendukung ApiGames, Codashop, dan DuniaGames
- **Fallback System**: Otomatis beralih ke provider lain jika satu provider gagal
- **JWT Authentication**: Sistem autentikasi yang aman
- **Multi-Language**: Mendukung bahasa Indonesia dan Inggris
- **Game ID Validation**: Validasi ID game sebelum melakukan transaksi
- **Transaction Management**: Manajemen transaksi lengkap dengan riwayat

## Game yang Didukung

- Mobile Legends: Bang Bang
- Free Fire
- PUBG Mobile
- Valorant
- Genshin Impact
- Arena of Valor
- League of Legends: Wild Rift
- Honkai Impact 3rd
- Ragnarok M: Eternal Love
- Point Blank

## Instalasi

1. Clone repository
```bash
git clone <repository-url>
cd ppob-game-api
```

2. Install dependencies
```bash
composer install
```

3. Copy file environment
```bash
cp .env.example .env
```

4. Generate application key
```bash
php artisan key:generate
```

5. Generate JWT secret
```bash
php artisan jwt:secret
```

6. Konfigurasi database di file `.env`
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ppob_game
DB_USERNAME=root
DB_PASSWORD=
```

7. Konfigurasi provider API di file `.env`
```env
# ApiGames
APIGAMES_BASE_URL=https://v1.apigames.id
APIGAMES_MERCHANT_ID=your_merchant_id
APIGAMES_SIGNATURE=your_signature_key

# Codashop
CODASHOP_BASE_URL=https://order.codashop.com
CODASHOP_API_KEY=your_api_key

# DuniaGames
DUNIAGAMES_BASE_URL=https://api.duniagames.co.id
DUNIAGAMES_API_KEY=your_api_key
```

8. Jalankan migrasi database
```bash
php artisan migrate
```

9. Jalankan server
```bash
php artisan serve
```

## API Endpoints

### Authentication

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/auth/register` | Register user baru |
| POST | `/api/auth/login` | Login user |
| POST | `/api/auth/logout` | Logout user |
| POST | `/api/auth/refresh` | Refresh JWT token |
| GET | `/api/auth/profile` | Get profile user |

### Games

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/games` | Get daftar semua game |
| GET | `/api/games/{code}` | Get detail game |
| POST | `/api/games/check-id` | Cek ID/username game |
| GET | `/api/games/providers` | Get daftar provider |
| GET | `/api/games/provider/{provider}` | Get game berdasarkan provider |

### Transactions

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/transactions` | Get riwayat transaksi |
| GET | `/api/transactions/{id}` | Get detail transaksi |
| POST | `/api/transactions/topup` | Buat transaksi topup |
| POST | `/api/transactions/{id}/cancel` | Cancel transaksi |

### Utility

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/` | API information |
| GET | `/api/health` | Health check |

## Contoh Penggunaan

### 1. Register User

```bash
curl -X POST http://localhost:8000/api/auth/register   -H "Content-Type: application/json"   -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

### 2. Login

```bash
curl -X POST http://localhost:8000/api/auth/login   -H "Content-Type: application/json"   -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

### 3. Cek ID Game

```bash
curl -X POST http://localhost:8000/api/games/check-id   -H "Content-Type: application/json"   -d '{
    "game_code": "mobilelegends",
    "user_id": "123456789",
    "zone_id": "1234"
  }'
```

### 4. Topup Game

```bash
curl -X POST http://localhost:8000/api/transactions/topup   -H "Content-Type: application/json"   -H "Authorization: Bearer YOUR_JWT_TOKEN"   -d '{
    "game_code": "mobilelegends",
    "user_id": "123456789",
    "zone_id": "1234",
    "product_code": "ML_5_DIAMOND",
    "amount": 5000
  }'
```

## Response Format

Semua response API menggunakan format JSON standar:

### Success Response
```json
{
  "success": true,
  "message": "Success message",
  "data": {
    // Response data
  }
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    // Validation errors (jika ada)
  }
}
```

## Multi-Language Support

API mendukung bahasa Indonesia dan Inggris. Untuk mengatur bahasa, gunakan salah satu cara berikut:

1. Header `Accept-Language`: `id` atau `en`
2. Header `X-Locale`: `id` atau `en`
3. Query parameter: `?lang=id` atau `?lang=en`

## Provider Fallback System

API menggunakan sistem fallback otomatis:

1. Jika provider yang dipilih gagal, sistem akan mencoba provider lain
2. Prioritas provider: ApiGames → Codashop → DuniaGames
3. Jika semua provider gagal, akan mengembalikan error

## Rate Limiting

- 60 requests per menit per IP
- 1000 requests per jam per IP

## Error Codes

| Code | Description |
|------|-------------|
| 200 | Success |
| 201 | Created |
| 400 | Bad Request |
| 401 | Unauthorized |
| 404 | Not Found |
| 422 | Validation Error |
| 429 | Too Many Requests |
| 500 | Internal Server Error |

## Development

### Struktur Project

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       ├── AuthController.php
│   │       ├── GameController.php
│   │       └── TransactionController.php
│   └── Middleware/
│       └── SetLocale.php
├── Models/
│   ├── User.php
│   ├── Game.php
│   └── Transaction.php
└── Services/
    ├── ApiGamesService.php
    ├── CodashopService.php
    ├── DuniaGamesService.php
    └── GameProviderFactory.php
```

### Testing

```bash
# Run tests
php artisan test

# Run specific test
php artisan test --filter AuthTest
```

## Contributing

1. Fork repository
2. Buat feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push ke branch (`git push origin feature/amazing-feature`)
5. Buat Pull Request

## License

This project is licensed under the MIT License.

## Support

Untuk pertanyaan atau dukungan, silakan buat issue di repository ini.
