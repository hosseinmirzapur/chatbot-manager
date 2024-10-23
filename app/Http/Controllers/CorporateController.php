<?php

namespace App\Http\Controllers;


use App\Models\Corporate;
use Illuminate\Http\JsonResponse;

class CorporateController extends Controller
{
    /**
     * @param Corporate $corporate
     * @return JsonResponse
     */
    public function chat(Corporate $corporate): JsonResponse
    {
        $chat = $corporate->chats()->create();

        return response()->json([
            'chat' => $chat
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
