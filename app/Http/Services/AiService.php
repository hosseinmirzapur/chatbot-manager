<?php

namespace App\Http\Services;

use App\Exceptions\CustomException;
use Exception;
use Illuminate\Support\Facades\Http;

class AiService
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct()
    {
        $this->baseUrl = strval(config('services.ai.base_url'));
    }

    public function setApiKey(string $apiKey): AiService
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * @param string $message
     * @return array
     * @throws CustomException
     */
    public function sendToBot(string $message): array
    {
        $textConverse = strval(config('services.ai.chat.converse.text'));
        $postUrl = $this->baseUrl . $textConverse;
        $postData = [
            'request' => json_encode([
                'chat' => [
                    'messages' => [
                        [
                            'role' => 'assistant',
                            'content' => 'string'
                        ]
                    ]
                ],
                'bot' => 'tci'
            ]),
            'user_message' => $message
        ];
        try {
            $response = Http::asForm()
                ->withHeaders([
                    'Accept' => 'application/json',
                    'API-Key' => $this->apiKey,
                ])
                ->post($postUrl, $postData);

            if (!$response->successful()) {
                throw new CustomException($response->body());
            }
            $data = $response->json('assistant_message');

            if (isset($data['content'])) {
                return [
                    'message' => $data['content']
                ];
            }

            throw new CustomException('bot has no response');

        } catch (Exception $e) {
            throw new CustomException($e->getMessage());
        }
    }

    /**
     * @param string $botType
     * @return array
     * @throws CustomException
     */
    public function starterMessage(string $botType): array
    {
        $supportedBotTypes = array_values(config('services.ai.static_bot_types'));

        if (!in_array($botType, $supportedBotTypes)) {
            throw new CustomException('bot type not supported');
        }

        $starterMessageEndpoint = strval(config('services.ai.chat.converse.starter-message'));
        $postUrl = $this->baseUrl . $starterMessageEndpoint;
        try {
            $response = Http::withQueryParameters([
                'bot_type' => $botType,
            ])
                ->withHeaders([
                    'Accept' => 'application/json',
                    'API-Key' => $this->apiKey,
                ])
                ->post($postUrl);

            if (!$response->successful()) {
                throw new CustomException($response->body());
            }

            $bot_res = $response->json();

            return [
                'message' => $bot_res
            ];
        } catch (Exception $e) {
            throw new CustomException($e->getMessage());
        }
    }

}
