<?php

namespace App\Http\Services;

use App\Exceptions\CustomException;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

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
     * @param array $userData
     * @return array
     * @throws CustomException
     */
    public function sendToBot(array $userData): array
    {
        if (!$this->apiKey) {
            throw new CustomException('set api key before usage');
        }
        if (isset($userData['voice'])) {
            // Ensure that the file exists and is readable
            if (!file_exists($userData['voice']) || !is_readable($userData['voice'])) {
                throw new CustomException('Audio file is not accessible');
            }
            $postUrl = $this->baseUrl . config('services.ai.chat.converse.audio');
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
            ];
        } else {
            $postUrl = $this->baseUrl . config('services.ai.chat.converse.text');
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
                'user_message' => $userData['text']
            ];
        }

        try {
            $http = isset($userData['voice']) ? Http::asMultipart()
                ->attach(
                    'audio_file',
                    fopen($userData['voice'], 'r'),
                    Str::random(10) . '.' . $userData['voice']->getClientOriginalExtension()
                ) : Http::asForm();
            $response = $http
                ->withHeaders([
                    'Accept' => 'application/json',
                    'API-Key' => $this->apiKey,
                ])
                ->post($postUrl, $postData);

            if (!$response->successful()) {
                throw new CustomException($response->body());
            }
            $assistant = $response->json('assistant_message');
            $user = $response->json('user_message');

            if (isset($assistant['content'])) {
                return [
                    'message' => $assistant['content'],
                    'user_message' => $user['content']
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
        if (!$this->apiKey) {
            throw new CustomException('set api key before usage');
        }

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
