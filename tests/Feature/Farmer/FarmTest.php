<?php

namespace Tests\Feature\Farmer;

use App\Models\Farm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FarmTest extends TestCase
{
    use RefreshDatabase;

    public function test_farmer_can_create_farm(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/farmer/farms', [
            'name'      => 'Al-Wadi Farm',
            'latitude'  => 33.5138,
            'longitude' => 36.2765,
            'area'      => 25.5,
        ])
        ->assertCreated()
        ->assertJsonPath('data.name', 'Al-Wadi Farm')
        ->assertJsonPath('message', 'Farm created successfully.');
    }

    public function test_farmer_can_delete_farm(): void
    {
        $user = User::factory()->create();
        $farm = Farm::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/v1/farmer/farms/{$farm->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Farm deleted successfully.');

        $this->assertSoftDeleted('farms', ['id' => $farm->id]);
    }


    public function test_show_returns_not_found_message_for_missing_farm(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/farmer/farms/999')
            ->assertNotFound()
            ->assertJsonPath('message', 'Farm not found.');
    }


    public function test_farmer_cannot_view_another_farmers_farm(): void
    {
        $user  = User::factory()->create();
        $other = Farm::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->getJson("/api/v1/farmer/farms/{$other->id}")
            ->assertForbidden();
    }
}
