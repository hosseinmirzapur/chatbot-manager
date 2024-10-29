<?php

namespace Tests\Feature;

use App\Models\Chat;
use App\Models\Corporate;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class ApiTest extends TestCase
{
    public function test_it_returns_404_if_corporate_does_not_exist()
    {
        // Try deleting a non-existent corporate
        $response = $this->postJson("/api/corporates/99999/delete"); // Non-existent ID

        // Assert 404 Not Found response
        $response->assertStatus(404);
    }

    public function test_it_sends_a_message_to_the_chat_and_receives_a_response_from_the_bot()
    {
        $baseUrl = strval(config('services.ai.base_url'));
        $textConverse = strval(config('services.ai.chat.converse.text'));

        Http::fake([
            $baseUrl . $textConverse => Http::response([
                'assistant_message' => [
                    'content' => 'Hello from the bot'
                ]
            ])
        ]);

        // Create a corporate record
        $corporate = Corporate::query()->create([
            'name' => Str::random(10),
            'status' => Corporate::STATUSES[1],
            'chat_bg' => 'chat_bg.jpg',
            'logo' => 'logo.jpg',
            'api_key' => env('AI_API_KEY')
        ]);

        // Create a chat record
        /** @var Chat $chat */
        $chat = $corporate->chats()->create();

        // Prepare the request data
        $data = [
            'text' => 'Hello, bot!'
        ];

        $response = $this->postJson('/api/chats/' . $chat->slug . '/messages', $data);
        $response->assertStatus(200);

        $this->assertDatabaseHas('messages', [
            'chat_id' => $chat->id,
            'text' => $data['text'],
            'role' => 'user'
        ]);

        $this->assertDatabaseHas('messages', [
            'chat_id' => $chat->id,
            'text' => 'Hello from the bot',
        ]);
    }

    public function test_it_sends_voice_file_and_receives_answer()
    {
        $corporate = Corporate::query()->create([
            'name' => Str::random(10),
            'status' => Corporate::STATUSES[1],
            'chat_bg' => 'chat_bg.jpg',
            'logo' => 'logo.jpg',
            'api_key' => env('AI_API_KEY')
        ]);

        /** @var Chat $chat */
        $chat = $corporate->chats()->create();

        $file = public_path('/hello.mp3');

        $response = $this->postJson("/api/chats/$chat->slug/messages", [
            'voice' => file_get_contents($file)
        ]);

        $response->assertStatus(200)->assertJsonStructure([
            'user', 'bot'
        ]);
    }

    public function test_sending_not_wav_voice_file_fails()
    {
        $corporate = Corporate::query()->create([
            'name' => Str::random(10),
            'status' => Corporate::STATUSES[1],
            'chat_bg' => 'chat_bg.jpg',
            'logo' => 'logo.jpg',
            'api_key' => env('AI_API_KEY')
        ]);

        /** @var Chat $chat */
        $chat = $corporate->chats()->create();

        $file = UploadedFile::fake()
            ->create('voice_file.mp3', 6000, 'audio/mp3');

        $response = $this->postJson("/api/chats/$chat->slug/messages", [
            'voice' => $file
        ]);

        $response->assertStatus(422);
    }
}
