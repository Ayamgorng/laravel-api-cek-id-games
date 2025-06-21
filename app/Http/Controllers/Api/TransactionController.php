<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GameProviderFactory;
use App\Models\Transaction;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    /**
     * Get riwayat transaksi user
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $status = $request->get('status');
            $gameCode = $request->get('game_code');
            $perPage = $request->get('per_page', 15);

            $query = Transaction::where('user_id', $user->id)
                ->with(['game'])
                ->orderBy('created_at', 'desc');

            if ($status) {
                $query->where('status', $status);
            }

            if ($gameCode) {
                $query->whereHas('game', function($q) use ($gameCode) {
                    $q->where('code', $gameCode);
                });
            }

            $transactions = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => __('messages.success'),
                'data' => $transactions
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting transactions: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => __('messages.server_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detail transaksi
     */
    public function show($transactionId)
    {
        try {
            $user = Auth::user();
            
            $transaction = Transaction::where('transaction_id', $transactionId)
                ->where('user_id', $user->id)
                ->with(['game'])
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.transaction.transaction_not_found')
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => __('messages.success'),
                'data' => [
                    'transaction' => $transaction
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting transaction detail: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => __('messages.server_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buat transaksi topup baru (struktur dasar)
     */
    public function topup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'game_code' => 'required|string',
            'user_id' => 'required|string',
            'zone_id' => 'nullable|string',
            'product_code' => 'required|string',
            'amount' => 'required|numeric|min:1000',
            'provider' => 'nullable|string|in:apigames,codashop,duniagames'
        ], [
            'game_code.required' => __('messages.game.game_code_required'),
            'user_id.required' => __('messages.game.id_required'),
            'product_code.required' => __('messages.transaction.product_required'),
            'amount.required' => __('messages.transaction.amount_required'),
            'amount.min' => 'Minimal amount adalah 1000',
            'provider.in' => __('messages.provider.not_supported')
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __('messages.validation_error'),
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $gameCode = $request->game_code;
            $userId = $request->user_id;
            $zoneId = $request->zone_id;
            $productCode = $request->product_code;
            $amount = $request->amount;
            $preferredProvider = $request->provider;

            // Cek apakah game didukung
            $allGames = GameProviderFactory::getAllSupportedGames();
            if (!isset($allGames[$gameCode])) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.game.game_not_supported')
                ], 404);
            }

            // Cek apakah game memerlukan zone_id
            if (in_array('zone_id', $allGames[$gameCode]['fields']) && empty($zoneId)) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.game.zone_id_required')
                ], 422);
            }

            // Cek ID game terlebih dahulu
            $checkResult = GameProviderFactory::checkGameIdWithFallback(
                $gameCode, 
                $userId, 
                $zoneId, 
                $preferredProvider
            );

            if (!$checkResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.game.id_check_failed'),
                    'details' => $checkResult['message']
                ], 404);
            }

            // Generate transaction ID
            $transactionId = 'TRX-' . strtoupper(Str::random(10)) . '-' . time();

            // Buat record transaksi
            $transaction = Transaction::create([
                'transaction_id' => $transactionId,
                'user_id' => $user->id,
                'game_id' => 1, // Sementara hardcode, nanti bisa disesuaikan dengan database
                'game_user_id' => $userId,
                'game_username' => $checkResult['data']['username'] ?? null,
                'product_code' => $productCode,
                'product_name' => 'Product ' . $productCode, // Sementara, nanti bisa dari database produk
                'amount' => $amount,
                'status' => 'pending',
                'provider' => $checkResult['provider']
            ]);

            // Proses topup (ini adalah struktur dasar, implementasi sebenarnya tergantung provider)
            $provider = GameProviderFactory::create($checkResult['provider']);
            $topupResult = $provider->topup($gameCode, $userId, $productCode, $amount, $zoneId);

            // Update status transaksi berdasarkan hasil topup
            if ($topupResult['success']) {
                $transaction->update([
                    'status' => 'success',
                    'provider_response' => $topupResult['data'],
                    'processed_at' => now()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => __('messages.transaction.topup_success'),
                    'data' => [
                        'transaction' => $transaction,
                        'provider_response' => $topupResult['data']
                    ]
                ]);
            } else {
                $transaction->update([
                    'status' => 'failed',
                    'provider_response' => $topupResult,
                    'processed_at' => now()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => __('messages.transaction.topup_failed'),
                    'data' => [
                        'transaction' => $transaction,
                        'error' => $topupResult['message']
                    ]
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Error processing topup: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => __('messages.server_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel transaksi
     */
    public function cancel($transactionId)
    {
        try {
            $user = Auth::user();
            
            $transaction = Transaction::where('transaction_id', $transactionId)
                ->where('user_id', $user->id)
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.transaction.transaction_not_found')
                ], 404);
            }

            if ($transaction->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi tidak dapat dibatalkan'
                ], 400);
            }

            $transaction->update([
                'status' => 'cancelled',
                'processed_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('messages.transaction.transaction_cancelled'),
                'data' => [
                    'transaction' => $transaction
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error cancelling transaction: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => __('messages.server_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
