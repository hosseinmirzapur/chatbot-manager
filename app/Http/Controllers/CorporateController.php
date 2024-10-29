<?php

namespace App\Http\Controllers;


use App\Exceptions\CustomException;
use App\Http\Services\AiService;
use App\Models\Chat;
use App\Models\Corporate;
use Illuminate\Http\JsonResponse;

class CorporateController extends Controller
{

    public function __construct(private readonly AiService $service)
    {

    }

    /**
     * @param Corporate $corporate
     * @return JsonResponse
     */
    public function show(Corporate $corporate): JsonResponse
    {
        return response()->json([
            'corporate' => $corporate
        ]);
    }

    /**
     * @param Corporate $corporate
     * @param string $botType
     * @return JsonResponse
     * @throws CustomException
     */
    public function chat(Corporate $corporate, string $botType): JsonResponse
    {
        // First try to get the starter message for the specific bot type
        $result = $this->service
            ->setApiKey($corporate->api_key)
            ->starterMessage($botType);

        // If successful create the chat with the starter message
        /** @var Chat $chat */
        $chat = $corporate->chats()->create();
        $starterMessage = $chat->messages()->create([
            'role' => 'bot',
            'text' => $result['message']
        ]);

        return response()->json([
            'chat' => $chat,
            'starter' => $starterMessage->text
        ]);
    }

    /**
     * @param Corporate $corporate
     * @return JsonResponse
     */
    public function chatHistory(Corporate $corporate): JsonResponse
    {
        $chats = $corporate->chats;

        return response()->json([
            'chats' => $chats
        ]);
    }
}
