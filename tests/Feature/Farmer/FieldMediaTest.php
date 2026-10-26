<?php

namespace Tests\Feature\Farmer;

use App\Models\Field;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FieldMediaTest extends TestCase
{
    use RefreshDatabase;

    public function test_farmer_can_upload_and_delete_field_media(): void
    {
        Storage::fake('public');
        $user  = User::factory()->create();
        $field = Field::factory()->create();
        $field->farm->update(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/v1/farmer/fields/{$field->id}/media", [
                'files' => [UploadedFile::fake()->image('a.jpg'), UploadedFile::fake()->image('b.png')],
            ])
            ->assertCreated()
            ->assertJsonCount(2, 'data');

        $id = $response->json('data.0.id');

        $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/v1/farmer/media/{$id}")
            ->assertOk()
            ->assertJsonPath('message', 'Media deleted successfully.');

        $this->assertDatabaseCount('media', 1);
    }

    public function test_farmer_cannot_upload_to_another_farmers_field(): void
    {
        Storage::fake('public');
        $user  = User::factory()->create();
        $field = Field::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->postJson("/api/v1/farmer/fields/{$field->id}/media", [
                'files' => [UploadedFile::fake()->image('a.jpg')],
            ])
            ->assertForbidden();
    }
}
