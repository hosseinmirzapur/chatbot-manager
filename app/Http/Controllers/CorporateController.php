<?php

namespace App\Http\Controllers;


use App\Http\Requests\CorporateRequest;
use App\Models\Corporate;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CorporateController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $corporates = Corporate::all();

        return response()->json([
            'corporates' => $corporates
        ]);
    }

    /**
     * @param CorporateRequest $request
     * @return JsonResponse
     */
    public function store(CorporateRequest $request): JsonResponse
    {
        $data = $request->validated();

        if (isset($data['chat_bg'])) {
            $chatBg = $data['chat_bg'];
            $extension = $chatBg->getClientOriginalExtension();
            $data['chat_bg'] = Storage::disk('liara')
                ->putFileAs(
                    '/corporates/backgrounds',
                    $data['chat_bg'],
                    Str::random(10) . $extension
                );
        }

        if (isset($data['logo'])) {
            $logo = $data['logo'];
            $extension = $logo->getClientOriginalExtension();
            $data['logo'] = Storage::disk('liara')
                    ->putFileAs(
                        '/corporates/logos',
                        $data['logo'],
                        Str::random(10) . $extension
                    );
        }

        $corp = Corporate::query()->create($data);

        return response()->json([
            'corporate' => $corp
        ]);
    }

    /**
     * @param CorporateRequest $request
     * @param Corporate $corporate
     * @return JsonResponse
     */
    public function update(CorporateRequest $request, Corporate $corporate): JsonResponse
    {
        $data = $request->validated();

        if (isset($data['chat_bg'])) {
            $chatBg = $data['chat_bg'];
            $extension = $chatBg->getClientOriginalExtension();
            $data['chat_bg'] = Storage::disk('liara')
                ->putFileAs(
                    '/corporates/backgrounds',
                    $data['chat_bg'],
                    Str::random(10) . $extension
                );
        }

        if (isset($data['logo'])) {
            $logo = $data['logo'];
            $extension = $logo->getClientOriginalExtension();
            $data['logo'] = Storage::disk('liara')
                ->putFileAs(
                    '/corporates/logos',
                    $data['logo'],
                    Str::random(10) . $extension
                );
        }

        $corporate->update($data);

        return response()->json([
            'corporate' => $corporate
        ]);
    }

    /**
     * @param Corporate $corporate
     * @return JsonResponse
     */
    public function destroy(Corporate $corporate): JsonResponse
    {
        $corporate->delete();
        return response()->json();
    }

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
