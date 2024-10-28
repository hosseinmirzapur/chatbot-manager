<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Http\Requests\ChatMessageRequest;
use App\Http\Services\AiService;
use App\Models\Chat;
use Illuminate\Http\JsonResponse;

class ChatController extends Controller
{
    public function __construct(private readonly AiService $service)
    {
    }


    /**
     * @param Chat $chat
     * @return JsonResponse
     */
    public function messages(Chat $chat): JsonResponse
    {
        $messages = $chat->messages;

        return response()->json([
            'messages' => $messages
        ]);
    }

    /**
     * @param ChatMessageRequest $request
     * @param Chat $chat
     * @return JsonResponse
     * @throws CustomException
     */
    public function sendMessage(ChatMessageRequest $request, Chat $chat): JsonResponse
    {
        $data = $request->validated();

        $userMessage = $chat->messages()->create([
            'text' => $data['text'],
            'role' => 'user'
        ]);

        $result = $this->service->sendToBot($data['text']);

        $botMessage = $chat->messages()->create([
            'text' => $result['message'],
            'role' => 'bot'
        ]);

        return response()->json([
            'user' => $userMessage,
            'bot' => $botMessage
        ]);
    }
}
