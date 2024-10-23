<?php

namespace App\Http\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class AiService
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct()
    {
        $this->baseUrl = strval(config('services.ai.base_url'));
        $this->apiKey = strval(config('services.ai.api_key'));
    }

    /**
     * @param string $message
     * @return array
     */
    public function sendToBot(string $message): array
    {
        $textConverse = strval(config('services.ai.chat.converse.text'));
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])
                ->post("$this->baseUrl/$textConverse", [
                    'request' => '{"chat":{"messages":[{"role":"assistant","content":"string"}]},"bot":"tci"}',
                    'user_message' => $message
                ]);

            $data = $response->json('assistant_message');

            if (isset($data['content'])) {
                return [
                    'message' => $data['content']
                ];
            }

            return [
                'error' => 'bot has no response'
            ];

        } catch (Exception $e) {
            return [
                'error' => $e->getMessage(),
            ];
        }
    }

}