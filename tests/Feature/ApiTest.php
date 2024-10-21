<?php

namespace Tests\Feature;

use App\Models\Corporate;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class ApiTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_it_stores_a_corporate_with_file_uploads(): void
    {
        Storage::fake('liara');

        $data = [
            'name' => Str::random(10),
            'status' => Corporate::STATUSES[0],
            'chat_bg' => UploadedFile::fake()->image('chat_bg.jpg'),
            'logo' => UploadedFile::fake()->image('logo.jpg'),
        ];

        $response = $this->postJson('/api/corporates', $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('corporates', [
            'name' => $data['name'],
            'status' => $data['status'],
        ]);

        $corporate = $response->json('corporate');

        Storage::disk('liara')
            ->assertExists('/corporates/backgrounds/' . basename($corporate['chat_bg']));
        Storage::disk('liara')
            ->assertExists('/corporates/logos/' . basename($corporate['logo']));
    }

    public function test_it_updates_a_corporate_with_file_uploads()
    {
        // Fake the storage disk
        Storage::fake('liara');

        // Create a corporate with initial data
        $corporate = Corporate::query()->create([
            'name' => Str::random(10),
            'status' => Corporate::STATUSES[0],
            'chat_bg' => 'old_chat_bg.jpg',
            'logo' => 'old_logo.jpg',
        ]);

        // Prepare new data including file uploads
        $data = [
            'name' => Str::random(10),
            'status' => Corporate::STATUSES[1], // Assuming there are multiple statuses
            'chat_bg' => UploadedFile::fake()->image('new_chat_bg.jpg'),
            'logo' => UploadedFile::fake()->image('new_logo.jpg'),
        ];

        // Send the Update request to update the corporate
        $response = $this->postJson('/api/corporates/' . $corporate->id . '/update', $data);

        // Assert the response is OK
        $response->assertStatus(200);

        // Assert the corporate was updated in the database
        $this->assertDatabaseHas('corporates', [
            'id' => $corporate->id,
            'name' => $data['name'],
            'status' => $data['status'],
        ]);

        // Assert the old files no longer exist on the storage disk
        Storage::disk('liara')->assertMissing('/corporates/backgrounds/old_chat_bg.jpg');
        Storage::disk('liara')->assertMissing('/corporates/logos/old_logo.jpg');

        // Assert the new files are stored correctly
        Storage::disk('liara')->assertExists('/corporates/backgrounds/' . basename($corporate->fresh()->chat_bg));
        Storage::disk('liara')->assertExists('/corporates/logos/' . basename($corporate->fresh()->logo));
    }

    public function test_it_deletes_a_corporate()
    {
        // Create a corporate record
        $corporate = Corporate::query()->create([
            'name' => Str::random(10),
            'status' => Corporate::STATUSES[0],
            'chat_bg' => 'chat_bg.jpg',
            'logo' => 'logo.jpg',
        ]);

        // Send the DELETE request to destroy the corporate
        $response = $this->postJson("/api/corporates/$corporate->id/delete");

        // Assert the response is OK
        $response->assertStatus(200);

        // Assert the corporate is soft-deleted from the database
        $this->assertDatabaseMissing('corporates', [
            'id' => $corporate->id,
        ]);
    }

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
            'status' => Corporate::STATUSES[0],
            'chat_bg' => 'chat_bg.jpg',
            'logo' => 'logo.jpg',
        ]);

        // Create a chat record
        $chat = $corporate->chats()->create();

        // Prepare the request data
        $data = [
            'text' => 'Hello, bot!'
        ];

        $response = $this->postJson('/api/chats/' . $chat->id . '/messages', $data);
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
}
