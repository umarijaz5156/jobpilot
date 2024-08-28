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

    // public function callApi($endpoint, $method = 'GET', $data = [], $apiIdentifier = 'default')
    // {
    //     $retries = 0;
    //     $baseUrl = rtrim(env('ESS_API_BASE_URL'), '/');
    //     $fullUrl = $baseUrl . '/' . ltrim($endpoint, '/');

    //     while ($retries < self::RETRY_COUNT) {
    //         try {
    //             $token = $this->getToken($apiIdentifier);
    //             $response = $this->client->request($method, $fullUrl, [
    //                 'headers' => [
    //                     'Authorization' => 'Bearer ' . $token,
    //                     'Ocp-Apim-Subscription-Key' => env('ESS_API_OCP_APIM_SUBSCRIPTION_KEY'),
    //                     'employment.gov.au-UniqueRequestMessageId' => Str::uuid()->toString()
    //                 ],
    //                 'json' => $data,
    //             ]);

    //             $statusCode = $response->getStatusCode();
    //             $content = json_decode($response->getBody(), true);
    //             // dd($data, $content);
    //             if ($statusCode >= 400) {
    //                 Log::error('API Error:', [
    //                     'endpoint' => $fullUrl,
    //                     'status_code' => $statusCode,
    //                     'response' => $content
    //                 ]);
    //                 throw new \Exception('API Error');
    //             }

    //             return $content;
    //         } catch (\GuzzleHttp\Exception\ClientException $e) {
    //             dd($e->getMessage());
    //             if ($e->getCode() === 401) {
    //                 $retries++;
    //                 $this->refreshToken($apiIdentifier);
    //                 continue;
    //             }

    //             Log::error('API Call Failed:', [
    //                 'endpoint' => $fullUrl,
    //                 'exception' => $e->getMessage()
    //             ]);
    //             throw $e;
    //         } catch (\Exception $e) {
    //             Log::error('API Call Failed:', [
    //                 'endpoint' => $fullUrl,
    //                 'exception' => $e->getMessage()
    //             ]);
    //             throw $e;
    //         }
    //     }

    //     throw new \Exception('Max retry limit reached');
    // }

    public function callApi($endpoint, $method = 'GET', $data = [], $extraHeaders = [], $apiIdentifier = 'default')
    {
        $retries = 0;
        $baseUrl = rtrim(env('ESS_API_BASE_URL'), '/');
        $fullUrl = $baseUrl . '/' . ltrim($endpoint, '/');

        while ($retries < self::RETRY_COUNT) {
            try {
                $token = $this->getToken($apiIdentifier);
                $defaultHeaders = [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                    'Ocp-Apim-Subscription-Key' => env('ESS_API_OCP_APIM_SUBSCRIPTION_KEY'),
                    'employment.gov.au-UniqueRequestMessageId' => Str::uuid()->toString(),
                ];

                $headers = array_merge($defaultHeaders, $extraHeaders);

                $response = $this->client->request($method, $fullUrl, [
                    'headers' => $headers,
                    'json' => $data,
                ]);

                $statusCode = $response->getStatusCode();
                $content = json_decode($response->getBody(), true);

                if ($statusCode >= 400) {
                    Log::error('API Error:', [
                        'endpoint' => $fullUrl,
                        'status_code' => $statusCode,
                        'response' => $content,
                    ]);
                    throw new \Exception('API Error');
                }

                return $content;
            } catch (\GuzzleHttp\Exception\ServerException $e) {
                $request = $e->getRequest();
                $response = $e->hasResponse() ? $e->getResponse() : null;

                $statusCode = $response ? $response->getStatusCode() : null;
                $responseBody = $response ? $response->getBody()->getContents() : null;
                $requestHeaders = $request ? $request->getHeaders() : [];
                $requestBody = $request ? (string) $request->getBody() : '';

                Log::error('API Request Exception:', [
                    'endpoint' => $fullUrl,
                    'status_code' => $statusCode,
                    'response' => $responseBody,
                    'request_headers' => $requestHeaders,
                    'request_body' => $requestBody,
                    'exception' => $e->getMessage(),
                ]);

                // dd($e->getMessage(), $statusCode, $responseBody, $requestHeaders, $requestBody);

                throw $e;
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                $request = $e->getRequest();
                $response = $e->hasResponse() ? $e->getResponse() : null;

                $statusCode = $response ? $response->getStatusCode() : null;
                $responseBody = $response ? $response->getBody()->getContents() : null;
                $requestHeaders = $request ? $request->getHeaders() : [];
                $requestBody = $request ? (string) $request->getBody() : '';

                Log::error('API Request Exception:', [
                    'endpoint' => $fullUrl,
                    'status_code' => $statusCode,
                    'response' => $responseBody,
                    'request_headers' => $requestHeaders,
                    'request_body' => $requestBody,
                    'exception' => $e->getMessage(),
                ]);

                if ($statusCode === 401) {
                    $retries++;
                    $this->refreshToken($apiIdentifier);
                    continue;
                }

                // dd($e->getMessage(), $statusCode, $responseBody, $requestHeaders, $requestBody);

                throw $e;
            } catch (\Exception $e) {
                Log::error('API Call Failed:', [
                    'endpoint' => $fullUrl,
                    'exception' => $e->getMessage(),
                ]);
                // dd($e);
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
            $response = $this->client->post(env('ESS_API_OAUTH2_TOKEN_ENDPOINT'), [
                'form_params' => [
                    'resource' => env('ESS_API_WEB_API_RESOURCE_IDENTIFIER'),
                    'client_id' => env('ESS_API_CLIENT_ID'),
                    'client_assertion_type' => env('ESS_API_CLIENT_ASSERTION_TYPE'),
                    'client_assertion' => env('ESS_API_CLIENT_ASSERTION'),
                    'grant_type' => env('ESS_API_GRANT_TYPE'),
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
