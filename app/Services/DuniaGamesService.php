<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class DuniaGamesService
{
    private $client;
    private $baseUrl;
    private $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->baseUrl = config('services.duniagames.base_url');
        $this->apiKey = config('services.duniagames.api_key');
    }

    /**
     * Cek username/ID game
     */
    public function checkGameId($gameCode, $userId, $zoneId = null)
    {
        try {
            $url = "{$this->baseUrl}/api/transaction/v1/top-up/inquiry/store";
            
            $data = [
                'productId' => $this->getProductId($gameCode),
                'itemId' => $this->getDefaultItemId($gameCode),
                'catalogId' => $this->getCatalogId($gameCode),
                'paymentId' => 1, // Default payment method
                'gameId' => $userId,
                'zoneId' => $zoneId ?? '',
                'email' => 'test@example.com',
                'checkoutId' => time() . rand(1000, 9999)
            ];

            $response = $this->client->post($url, [
                'json' => $data,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                    'User-Agent' => 'DuniaGames-API-Client'
                ],
                'timeout' => 30
            ]);

            $result = json_decode($response->getBody(), true);

            // Parse response DuniaGames
            if (isset($result['success']) && $result['success']) {
                return [
                    'success' => true,
                    'data' => [
                        'username' => $result['data']['gameUserName'] ?? 'User ditemukan',
                        'user_id' => $userId,
                        'zone_id' => $zoneId
                    ],
                    'provider' => 'duniagames'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $result['message'] ?? 'ID tidak ditemukan',
                    'provider' => 'duniagames'
                ];
            }

        } catch (RequestException $e) {
            Log::error('DuniaGames Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Gagal mengecek ID game',
                'error' => $e->getMessage(),
                'provider' => 'duniagames'
            ];
        }
    }

    /**
     * Get Product ID berdasarkan game
     */
    private function getProductId($gameCode)
    {
        $productIds = [
            'mobilelegends' => 1,
            'freefire' => 2,
            'pubgmobile' => 3,
            'aov' => 4,
            'wildrift' => 5,
            'valorant' => 6,
            'genshinimpact' => 7,
            'honkaiimpact' => 8,
            'ragnarokmobile' => 9,
            'pointblank' => 10
        ];

        return $productIds[$gameCode] ?? 1;
    }

    /**
     * Get Catalog ID berdasarkan game
     */
    private function getCatalogId($gameCode)
    {
        $catalogIds = [
            'mobilelegends' => 1,
            'freefire' => 2,
            'pubgmobile' => 3,
            'aov' => 4,
            'wildrift' => 5,
            'valorant' => 6,
            'genshinimpact' => 7,
            'honkaiimpact' => 8,
            'ragnarokmobile' => 9,
            'pointblank' => 10
        ];

        return $catalogIds[$gameCode] ?? 1;
    }

    /**
     * Get default item ID untuk testing
     */
    private function getDefaultItemId($gameCode)
    {
        $itemIds = [
            'mobilelegends' => 1,
            'freefire' => 1,
            'pubgmobile' => 1,
            'aov' => 1,
            'wildrift' => 1,
            'valorant' => 1,
            'genshinimpact' => 1,
            'honkaiimpact' => 1,
            'ragnarokmobile' => 1,
            'pointblank' => 1
        ];

        return $itemIds[$gameCode] ?? 1;
    }

    /**
     * Get daftar game yang didukung
     */
    public function getSupportedGames()
    {
        return [
            'mobilelegends' => [
                'name' => 'Mobile Legends: Bang Bang',
                'fields' => ['user_id', 'zone_id'],
                'icon' => 'https://cdn.duniagames.co.id/games/mobilelegends.png'
            ],
            'freefire' => [
                'name' => 'Free Fire',
                'fields' => ['user_id'],
                'icon' => 'https://cdn.duniagames.co.id/games/freefire.png'
            ],
            'pubgmobile' => [
                'name' => 'PUBG Mobile',
                'fields' => ['user_id'],
                'icon' => 'https://cdn.duniagames.co.id/games/pubgmobile.png'
            ],
            'aov' => [
                'name' => 'Arena of Valor',
                'fields' => ['user_id', 'zone_id'],
                'icon' => 'https://cdn.duniagames.co.id/games/aov.png'
            ],
            'wildrift' => [
                'name' => 'League of Legends: Wild Rift',
                'fields' => ['user_id'],
                'icon' => 'https://cdn.duniagames.co.id/games/wildrift.png'
            ],
            'valorant' => [
                'name' => 'Valorant',
                'fields' => ['user_id'],
                'icon' => 'https://cdn.duniagames.co.id/games/valorant.png'
            ],
            'genshinimpact' => [
                'name' => 'Genshin Impact',
                'fields' => ['user_id'],
                'icon' => 'https://cdn.duniagames.co.id/games/genshinimpact.png'
            ],
            'honkaiimpact' => [
                'name' => 'Honkai Impact 3rd',
                'fields' => ['user_id'],
                'icon' => 'https://cdn.duniagames.co.id/games/honkaiimpact.png'
            ],
            'ragnarokmobile' => [
                'name' => 'Ragnarok M: Eternal Love',
                'fields' => ['user_id'],
                'icon' => 'https://cdn.duniagames.co.id/games/ragnarokmobile.png'
            ],
            'pointblank' => [
                'name' => 'Point Blank',
                'fields' => ['user_id'],
                'icon' => 'https://cdn.duniagames.co.id/games/pointblank.png'
            ]
        ];
    }

    /**
     * Topup game (struktur dasar)
     */
    public function topup($gameCode, $userId, $productCode, $amount, $zoneId = null)
    {
        try {
            $url = "{$this->baseUrl}/api/transaction/v1/top-up/transaction/store";
            
            $data = [
                'productId' => $this->getProductId($gameCode),
                'itemId' => $productCode,
                'catalogId' => $this->getCatalogId($gameCode),
                'paymentId' => 1,
                'gameId' => $userId,
                'zoneId' => $zoneId ?? '',
                'email' => 'customer@example.com',
                'checkoutId' => time() . rand(1000, 9999),
                'amount' => $amount
            ];

            $response = $this->client->post($url, [
                'json' => $data,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json'
                ],
                'timeout' => 60
            ]);

            $result = json_decode($response->getBody(), true);

            return [
                'success' => true,
                'data' => $result,
                'provider' => 'duniagames'
            ];

        } catch (RequestException $e) {
            Log::error('DuniaGames Topup Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Gagal melakukan topup',
                'error' => $e->getMessage(),
                'provider' => 'duniagames'
            ];
        }
    }
}
