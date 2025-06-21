<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GameController;
use App\Http\Controllers\Api\TransactionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::get('/', function () {
    return response()->json([
        'success' => true,
        'message' => __('messages.api.welcome'),
        'version' => '1.0.0',
        'documentation' => url('/api/docs'),
        'endpoints' => [
            'auth' => [
                'register' => 'POST /api/auth/register',
                'login' => 'POST /api/auth/login',
                'logout' => 'POST /api/auth/logout',
                'refresh' => 'POST /api/auth/refresh',
                'profile' => 'GET /api/auth/profile'
            ],
            'games' => [
                'list' => 'GET /api/games',
                'detail' => 'GET /api/games/{code}',
                'check_id' => 'POST /api/games/check-id',
                'providers' => 'GET /api/games/providers',
                'by_provider' => 'GET /api/games/provider/{provider}'
            ],
            'transactions' => [
                'list' => 'GET /api/transactions',
                'detail' => 'GET /api/transactions/{id}',
                'topup' => 'POST /api/transactions/topup',
                'cancel' => 'POST /api/transactions/{id}/cancel'
            ]
        ]
    ]);
});

// Authentication routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    
    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('profile', [AuthController::class, 'profile']);
    });
});

// Game routes
Route::prefix('games')->group(function () {
    Route::get('/', [GameController::class, 'index']);
    Route::get('providers', [GameController::class, 'providers']);
    Route::get('provider/{provider}', [GameController::class, 'gamesByProvider']);
    Route::get('{gameCode}', [GameController::class, 'show']);
    Route::post('check-id', [GameController::class, 'checkId']);
});

// Transaction routes (protected)
Route::middleware('auth:api')->prefix('transactions')->group(function () {
    Route::get('/', [TransactionController::class, 'index']);
    Route::get('{transactionId}', [TransactionController::class, 'show']);
    Route::post('topup', [TransactionController::class, 'topup']);
    Route::post('{transactionId}/cancel', [TransactionController::class, 'cancel']);
});

// Health check
Route::get('health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is running',
        'timestamp' => now()->toISOString(),
        'version' => '1.0.0'
    ]);
});

// Fallback route
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Endpoint not found',
        'available_endpoints' => url('/api')
    ], 404);
});
