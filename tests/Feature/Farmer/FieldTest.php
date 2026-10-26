<?php

namespace Tests\Feature\Farmer;

use App\Models\Farm;
use App\Models\Field;
use App\Models\User;
use App\Services\Field\FieldService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FieldTest extends TestCase
{
    use RefreshDatabase;

    public function test_farmer_can_create_field(): void
    {
        $user = User::factory()->create();
        $farm = Farm::factory()->create(['user_id' => $user->id, 'area' => 50]);

        $this->actingAs($user, 'sanctum')
            ->postJson("/api/v1/farmer/farms/{$farm->id}/fields", [
                'name'      => 'North Field',
                'area'      => 20,
                'soil_type' => 'loamy',
            ])
            ->assertCreated()
            ->assertJsonPath('data.name', 'North Field')
            ->assertJsonPath('message', 'Field created successfully.');
    }

    public function test_total_fields_area_cannot_exceed_farm_area(): void
    {
        $user = User::factory()->create();
        $farm = Farm::factory()->create(['user_id' => $user->id, 'area' => 30]);
        Field::factory()->create(['farm_id' => $farm->id, 'area' => 25]);

        $this->actingAs($user, 'sanctum')
            ->postJson("/api/v1/farmer/farms/{$farm->id}/fields", [
                'name' => 'South Field',
                'area' => 10,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('area');
    }

    public function test_farmer_cannot_add_field_to_another_farmers_farm(): void
    {
        $user = User::factory()->create();
        $farm = Farm::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->postJson("/api/v1/farmer/farms/{$farm->id}/fields", [
                'name' => 'Stolen Field',
                'area' => 5,
            ])
            ->assertForbidden();
    }

    public function test_show_returns_not_found_message_for_missing_field(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/farmer/fields/999')
            ->assertNotFound()
            ->assertJsonPath('message', 'Field not found.');
    }
}
