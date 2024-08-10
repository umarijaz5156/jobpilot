<?php

namespace App\Services\API\EssAPI;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EssApiService
{
    const CACHE_KEY_PREFIX = 'ess_api_token_';
    const RETRY_COUNT = 3;

    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function callApi($endpoint, $method = 'GET', $data = [], $apiIdentifier = 'default')
    {
        $retries = 0;

        while ($retries < self::RETRY_COUNT) {
            try {
                $token = $this->getToken($apiIdentifier);
                $response = $this->client->request($method, $endpoint, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                        'Ocp-Apim-Subscription-Key' => env('OCP_APIM_SUBSCRIPTION_KEY'),
                        'employment.gov.au-UniqueRequestMessageId' => Str::uuid()->toString()
                    ],
                    'json' => $data,
                ]);

                $statusCode = $response->getStatusCode();
                $content = json_decode($response->getBody(), true);

                if ($statusCode >= 400) {
                    Log::error('API Error:', [
                        'endpoint' => $endpoint,
                        'status_code' => $statusCode,
                        'response' => $content
                    ]);
                    throw new \Exception('API Error');
                }

                return $content;
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                if ($e->getCode() === 401) {
                    $retries++;
                    $this->refreshToken($apiIdentifier);
                    continue;
                }

                Log::error('API Call Failed:', [
                    'endpoint' => $endpoint,
                    'exception' => $e->getMessage()
                ]);
                throw $e;
            } catch (\Exception $e) {
                Log::error('API Call Failed:', [
                    'endpoint' => $endpoint,
                    'exception' => $e->getMessage()
                ]);
                throw $e;
            }
        }

        throw new \Exception('Max retry limit reached');
    }

    protected function getToken($apiIdentifier)
    {
        $cacheKey = self::CACHE_KEY_PREFIX . $apiIdentifier;
        $token = Cache::get($cacheKey);

        if (!$token) {
            $token = $this->fetchNewToken($apiIdentifier);
            Cache::put($cacheKey, $token, 3600);
        }

        return $token;
    }

    protected function fetchNewToken($apiIdentifier)
    {
        try {
            $response = $this->client->post(env('OAUTH2_TOKEN_ENDPOINT'), [
                'form_params' => [
                    'resource' => env('WEB_API_RESOURCE_IDENTIFIER'),
                    'client_id' => env('CLIENT_ID'),
                    'client_assertion_type' => env('CLIENT_ASSERTION_TYPE'),
                    'client_assertion' => env('CLIENT_ASSERTION'),
                    'grant_type' => env('GRANT_TYPE'),
                ],
            ]);

            $tokenData = json_decode($response->getBody(), true);
            $token = $tokenData['access_token'];
            $expiresIn = $tokenData['expires_in'] - 60;

            $cacheKey = self::CACHE_KEY_PREFIX . $apiIdentifier;
            Cache::put($cacheKey, $token, $expiresIn);

            return $token;
        } catch (\Exception $e) {
            Log::error('Token Fetch Failed:', ['exception' => $e->getMessage()]);
            throw $e;
        }
    }

    protected function refreshToken($apiIdentifier)
    {
        $cacheKey = self::CACHE_KEY_PREFIX . $apiIdentifier;
        Cache::forget($cacheKey);
        return $this->fetchNewToken($apiIdentifier);
    }
}
