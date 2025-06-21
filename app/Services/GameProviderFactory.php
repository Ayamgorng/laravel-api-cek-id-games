<?php

namespace App\Services;

use App\Services\ApiGamesService;
use App\Services\CodashopService;
use App\Services\DuniaGamesService;

class GameProviderFactory
{
    /**
     * Buat instance provider berdasarkan nama provider
     */
    public static function create($provider)
    {
        switch (strtolower($provider)) {
            case 'apigames':
                return new ApiGamesService();
            case 'codashop':
                return new CodashopService();
            case 'duniagames':
                return new DuniaGamesService();
            default:
                throw new \InvalidArgumentException("Provider {$provider} tidak didukung");
        }
    }

    /**
     * Get semua provider yang tersedia
     */
    public static function getAvailableProviders()
    {
        return [
            'apigames' => [
                'name' => 'ApiGames',
                'description' => 'Provider resmi untuk berbagai game populer',
                'website' => 'https://apigames.id'
            ],
            'codashop' => [
                'name' => 'Codashop',
                'description' => 'Platform top-up game terpercaya',
                'website' => 'https://codashop.com'
            ],
            'duniagames' => [
                'name' => 'Dunia Games',
                'description' => 'Platform gaming Indonesia',
                'website' => 'https://duniagames.co.id'
            ]
        ];
    }

    /**
     * Get semua game yang didukung dari semua provider
     */
    public static function getAllSupportedGames()
    {
        $allGames = [];
        $providers = ['apigames', 'codashop', 'duniagames'];

        foreach ($providers as $providerName) {
            try {
                $provider = self::create($providerName);
                $games = $provider->getSupportedGames();
                
                foreach ($games as $gameCode => $gameData) {
                    if (!isset($allGames[$gameCode])) {
                        $allGames[$gameCode] = $gameData;
                        $allGames[$gameCode]['providers'] = [];
                    }
                    $allGames[$gameCode]['providers'][] = $providerName;
                }
            } catch (\Exception $e) {
                // Log error tapi lanjutkan dengan provider lain
                \Log::warning("Error loading games from provider {$providerName}: " . $e->getMessage());
            }
        }

        return $allGames;
    }

    /**
     * Cek ID game dengan mencoba semua provider yang mendukung game tersebut
     */
    public static function checkGameIdWithFallback($gameCode, $userId, $zoneId = null, $preferredProvider = null)
    {
        $allGames = self::getAllSupportedGames();
        
        if (!isset($allGames[$gameCode])) {
            return [
                'success' => false,
                'message' => 'Game tidak didukung',
                'game_code' => $gameCode
            ];
        }

        $providers = $allGames[$gameCode]['providers'];
        
        // Jika ada preferred provider, coba dulu
        if ($preferredProvider && in_array($preferredProvider, $providers)) {
            array_unshift($providers, $preferredProvider);
            $providers = array_unique($providers);
        }

        $lastError = null;

        foreach ($providers as $providerName) {
            try {
                $provider = self::create($providerName);
                $result = $provider->checkGameId($gameCode, $userId, $zoneId);
                
                if ($result['success']) {
                    return $result;
                }
                
                $lastError = $result;
            } catch (\Exception $e) {
                $lastError = [
                    'success' => false,
                    'message' => 'Error pada provider ' . $providerName,
                    'error' => $e->getMessage(),
                    'provider' => $providerName
                ];
            }
        }

        return $lastError ?? [
            'success' => false,
            'message' => 'Semua provider gagal',
            'game_code' => $gameCode
        ];
    }

    /**
     * Get provider terbaik untuk game tertentu
     */
    public static function getBestProviderForGame($gameCode)
    {
        // Prioritas provider berdasarkan reliabilitas dan kecepatan
        $providerPriority = [
            'apigames' => 1,
            'codashop' => 2,
            'duniagames' => 3
        ];

        $allGames = self::getAllSupportedGames();
        
        if (!isset($allGames[$gameCode])) {
            return null;
        }

        $availableProviders = $allGames[$gameCode]['providers'];
        $bestProvider = null;
        $bestPriority = 999;

        foreach ($availableProviders as $provider) {
            $priority = $providerPriority[$provider] ?? 999;
            if ($priority < $bestPriority) {
                $bestPriority = $priority;
                $bestProvider = $provider;
            }
        }

        return $bestProvider;
    }
}
