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
        /** @var Chat $chat */
        $chat = $corporate->chats()->create();
        $result = $this->service->starterMessage($botType);

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
