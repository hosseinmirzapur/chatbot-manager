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

        if (!isset($data['text']) && !isset($data['voice'])) {
            throw new CustomException('حداقل یکی از فیلد های متن یا صوت اجباری هستند', 422);
        }

        if (isset($data['text']) && isset($data['voice'])) {
            throw new CustomException('خطا! نمیتوان هم متن و هم صدا ارسال کرد.', 422);
        }

        $corporate = $chat->corporate;
        if ($corporate->status !== 'ACCEPTED' || !$corporate->api_key) {
            throw new CustomException('دسترسی غیر مجاز', 403);
        }

        $result = $this->service
            ->setApiKey($corporate->api_key)
            ->sendToBot($data);

        $userMessage = $chat->messages()->create([
            'text' => $data['text'] ?? $result['user_message'],
            'role' => 'user'
        ]);

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
