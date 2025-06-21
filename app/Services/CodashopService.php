<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class CodashopService
{
    private $client;
    private $baseUrl;
    private $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->baseUrl = config('services.codashop.base_url');
        $this->apiKey = config('services.codashop.api_key');
    }

    /**
     * Cek username/ID game
     */
    public function checkGameId($gameCode, $userId, $zoneId = null)
    {
        try {
            // Codashop menggunakan endpoint yang berbeda untuk setiap game
            $url = $this->getCheckIdUrl($gameCode);
            
            $params = [
                'voucherPricePoint.id' => $this->getDefaultProductId($gameCode),
                'voucherPricePoint.price' => '0',
                'voucherPricePoint.variablePrice' => '0',
                'user.userId' => $userId,
                'voucherTypeName' => $gameCode,
                'shopLang' => 'id_ID'
            ];

            // Tambahkan zone_id untuk game yang memerlukan
            if ($zoneId && in_array($gameCode, ['mobilelegends', 'aov'])) {
                $params['user.zoneId'] = $zoneId;
            }

            $response = $this->client->post($url, [
                'form_params' => $params,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ],
                'timeout' => 30
            ]);

            $data = json_decode($response->getBody(), true);

            // Parse response Codashop
            if (isset($data['success']) && $data['success']) {
                return [
                    'success' => true,
                    'data' => [
                        'username' => $data['confirmationFields']['username'] ?? 'User ditemukan',
                        'user_id' => $userId,
                        'zone_id' => $zoneId
                    ],
                    'provider' => 'codashop'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'ID tidak ditemukan',
                    'provider' => 'codashop'
                ];
            }

        } catch (RequestException $e) {
            Log::error('Codashop Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Gagal mengecek ID game',
                'error' => $e->getMessage(),
                'provider' => 'codashop'
            ];
        }
    }

    /**
     * Get URL untuk cek ID berdasarkan game
     */
    private function getCheckIdUrl($gameCode)
    {
        $urls = [
            'mobilelegends' => $this->baseUrl . '/id/mobile-legends',
            'freefire' => $this->baseUrl . '/id/free-fire',
            'pubgmobile' => $this->baseUrl . '/id/pubg-mobile',
            'aov' => $this->baseUrl . '/id/arena-of-valor',
            'wildrift' => $this->baseUrl . '/id/wild-rift',
            'valorant' => $this->baseUrl . '/id/valorant',
            'genshinimpact' => $this->baseUrl . '/id/genshin-impact'
        ];

        return $urls[$gameCode] ?? $this->baseUrl . '/id/' . $gameCode;
    }

    /**
     * Get default product ID untuk testing
     */
    private function getDefaultProductId($gameCode)
    {
        $productIds = [
            'mobilelegends' => '3',
            'freefire' => '1',
            'pubgmobile' => '1',
            'aov' => '1',
            'wildrift' => '1',
            'valorant' => '1',
            'genshinimpact' => '1'
        ];

        return $productIds[$gameCode] ?? '1';
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
                'icon' => 'https://cdn.codashop.com/games/mobilelegends.png'
            ],
            'freefire' => [
                'name' => 'Free Fire',
                'fields' => ['user_id'],
                'icon' => 'https://cdn.codashop.com/games/freefire.png'
            ],
            'pubgmobile' => [
                'name' => 'PUBG Mobile',
                'fields' => ['user_id'],
                'icon' => 'https://cdn.codashop.com/games/pubgmobile.png'
            ],
            'aov' => [
                'name' => 'Arena of Valor',
                'fields' => ['user_id', 'zone_id'],
                'icon' => 'https://cdn.codashop.com/games/aov.png'
            ],
            'wildrift' => [
                'name' => 'League of Legends: Wild Rift',
                'fields' => ['user_id'],
                'icon' => 'https://cdn.codashop.com/games/wildrift.png'
            ],
            'valorant' => [
                'name' => 'Valorant',
                'fields' => ['user_id'],
                'icon' => 'https://cdn.codashop.com/games/valorant.png'
            ],
            'genshinimpact' => [
                'name' => 'Genshin Impact',
                'fields' => ['user_id'],
                'icon' => 'https://cdn.codashop.com/games/genshinimpact.png'
            ]
        ];
    }

    /**
     * Topup game (struktur dasar)
     */
    public function topup($gameCode, $userId, $productCode, $amount, $zoneId = null)
    {
        try {
            $url = $this->getCheckIdUrl($gameCode);
            
            $data = [
                'voucherPricePoint.id' => $productCode,
                'voucherPricePoint.price' => $amount,
                'user.userId' => $userId,
                'voucherTypeName' => $gameCode,
                'shopLang' => 'id_ID'
            ];

            if ($zoneId) {
                $data['user.zoneId'] = $zoneId;
            }

            $response = $this->client->post($url, [
                'form_params' => $data,
                'timeout' => 60
            ]);

            $result = json_decode($response->getBody(), true);

            return [
                'success' => true,
                'data' => $result,
                'provider' => 'codashop'
            ];

        } catch (RequestException $e) {
            Log::error('Codashop Topup Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Gagal melakukan topup',
                'error' => $e->getMessage(),
                'provider' => 'codashop'
            ];
        }
    }
}
