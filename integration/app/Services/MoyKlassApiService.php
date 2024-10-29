<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;

class MoyKlassApiService
{
    protected Client $client;
    protected string $baseUri = 'https://api.moyklass.com/v1/';
    protected mixed $token;
    protected int $maxRetries = 5;
    protected int $retryDelay = 5;
    protected string $apiKey = 'M6M8PdBCjAvgAry1McKmiHrmrb0n6Wu6VbvFFcGt409nSaUQOP';

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => $this->baseUri,
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);
        $this->token = $this->getToken();
    }

    /**
     * Получение x-access-token с помощью API ключа.
     */
    public function getToken()
    {
        try {
            $response = $this->client->post('company/auth/getToken', [
                RequestOptions::JSON => ['apiKey' => $this->apiKey]
            ]);

            $result = json_decode($response->getBody(), true);
            return $result['accessToken'] ?? null;

        } catch (RequestException $e) {

            if ($e->hasResponse()) {
                return [
                    'error' => true,
                    'message' => $e->getResponse()->getBody()->getContents()
                ];
            }
            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Получение записи уроков с проверкой на "TooManyRequests".
     */
    public function call($url, $data = [], $method = 'GET')
    {
        $attempt = 0;
        do {
            try {
                $options = [
                    'headers' => [
                        'x-access-token' => $this->token
                    ]
                ];

                if (strtoupper($method) === 'GET') {
                    $options['query'] = $data;
                } else {
                    $options['json'] = $data;
                }

                $response = $this->client->request($method, $url, $options);

                $result = json_decode($response->getBody(), true);

                if (isset($result['code']) && $result['code'] === 'TooManyRequests') {
                    $attempt++;
                    sleep($this->retryDelay); // Задержка перед повторной попыткой
                } else {
                    return $result;
                }

            } catch (RequestException $e) {
                if ($e->hasResponse()) {
                    return [
                        'error' => true,
                        'message' => $e->getResponse()->getBody()->getContents()
                    ];
                }

                return [
                    'error' => true,
                    'message' => $e->getMessage()
                ];
            }

        } while ($attempt < $this->maxRetries);

        return [
            'error' => true,
            'message' => 'Превышено количество попыток запросов.'
        ];
    }
}
