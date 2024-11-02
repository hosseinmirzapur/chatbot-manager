<?php

namespace App\Http\Services;

use App\Exceptions\CustomException;
use Exception;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Audio\Mp3;
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
            throw new CustomException('ابتدا api key را ست کنید.');
        }
        if (isset($userData['voice'])) {
            // Ensure that the file exists and is readable
            if (!file_exists($userData['voice']) || !is_readable($userData['voice'])) {
                throw new CustomException('فایل صوتی غیر قابل دسترس است');
            }

            // Convert input voice to mp3
            $this->convertToMP3($userData);

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
                    $userData['voice'],
                    Str::random(10) . '.mp3'
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

            throw new CustomException('چت بات پاسخی ندارد');

        } catch (Exception $e) {
            throw new CustomException('خطایی در پاسخ دهی چت بات رخ داده است');
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
            throw new CustomException('این نوع چت بات پشتیبانی نمیشود');
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
            throw new CustomException('خطایی در پاسخ دهی چت بات رخ داده است');
        }
    }

    /**
     * @param array $data
     * @return void
     */
    private function convertToMP3(array &$data): void
    {
        $convertor = FFMpeg::create();

        $audio = $convertor->open($data['voice']);
        $format = new Mp3();

        // Use a buffer to store the converted audio content
        $tempFile = tempnam(sys_get_temp_dir(), 'audio_') . '.mp3';

        // Convert and write to the output buffer
        $audio->save($format, $tempFile);

        $audioContent = file_get_contents($tempFile);

        // Close the buffer
        unlink($tempFile);

        // set `voice` parameter as intended
        $data['voice'] = $audioContent;
    }

}
