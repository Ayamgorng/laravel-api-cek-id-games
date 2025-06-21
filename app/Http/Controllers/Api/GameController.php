<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GameProviderFactory;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class GameController extends Controller
{
    /**
     * Get daftar semua game yang didukung
     */
    public function index(Request $request)
    {
        try {
            $provider = $request->get('provider');
            $category = $request->get('category');
            
            if ($provider) {
                // Get games dari provider tertentu
                $providerService = GameProviderFactory::create($provider);
                $games = $providerService->getSupportedGames();
                
                return response()->json([
                    'success' => true,
                    'message' => __('messages.success'),
                    'data' => [
                        'games' => $games,
                        'provider' => $provider
                    ]
                ]);
            } else {
                // Get semua games dari semua provider
                $allGames = GameProviderFactory::getAllSupportedGames();
                
                return response()->json([
                    'success' => true,
                    'message' => __('messages.success'),
                    'data' => [
                        'games' => $allGames,
                        'total' => count($allGames)
                    ]
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error getting games: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => __('messages.server_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detail game berdasarkan kode
     */
    public function show($gameCode)
    {
        try {
            $allGames = GameProviderFactory::getAllSupportedGames();
            
            if (!isset($allGames[$gameCode])) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.game.game_not_supported')
                ], 404);
            }
            
            $gameData = $allGames[$gameCode];
            $gameData['best_provider'] = GameProviderFactory::getBestProviderForGame($gameCode);
            
            return response()->json([
                'success' => true,
                'message' => __('messages.success'),
                'data' => [
                    'game' => $gameData,
                    'game_code' => $gameCode
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting game detail: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => __('messages.server_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cek ID/Username game
     */
    public function checkId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'game_code' => 'required|string',
            'user_id' => 'required|string',
            'zone_id' => 'nullable|string',
            'provider' => 'nullable|string|in:apigames,codashop,duniagames'
        ], [
            'game_code.required' => __('messages.game.game_code_required'),
            'user_id.required' => __('messages.game.id_required'),
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
            $gameCode = $request->game_code;
            $userId = $request->user_id;
            $zoneId = $request->zone_id;
            $preferredProvider = $request->provider;

            // Cek apakah game memerlukan zone_id
            $allGames = GameProviderFactory::getAllSupportedGames();
            if (isset($allGames[$gameCode]) && 
                in_array('zone_id', $allGames[$gameCode]['fields']) && 
                empty($zoneId)) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.game.zone_id_required')
                ], 422);
            }

            // Cek ID dengan fallback ke provider lain jika gagal
            $result = GameProviderFactory::checkGameIdWithFallback(
                $gameCode, 
                $userId, 
                $zoneId, 
                $preferredProvider
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => __('messages.game.id_check_success'),
                    'data' => $result['data'],
                    'provider' => $result['provider']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? __('messages.game.id_check_failed'),
                    'provider' => $result['provider'] ?? null
                ], 404);
            }

        } catch (\Exception $e) {
            Log::error('Error checking game ID: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => __('messages.server_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get daftar provider yang tersedia
     */
    public function providers()
    {
        try {
            $providers = GameProviderFactory::getAvailableProviders();
            
            return response()->json([
                'success' => true,
                'message' => __('messages.success'),
                'data' => [
                    'providers' => $providers,
                    'total' => count($providers)
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting providers: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => __('messages.server_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get game berdasarkan provider
     */
    public function gamesByProvider($provider)
    {
        try {
            $providerService = GameProviderFactory::create($provider);
            $games = $providerService->getSupportedGames();
            
            return response()->json([
                'success' => true,
                'message' => __('messages.success'),
                'data' => [
                    'games' => $games,
                    'provider' => $provider,
                    'total' => count($games)
                ]
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.provider.not_supported')
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error getting games by provider: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => __('messages.server_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
