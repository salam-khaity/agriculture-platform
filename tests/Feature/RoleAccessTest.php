<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_gets_unauthenticated_message(): void
    {
        $this->getJson('/api/v1/farmer/farms')
            ->assertUnauthorized()
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function test_admin_cannot_use_farmer_routes(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $this->actingAs($admin, 'sanctum')->getJson('/api/v1/farmer/farms')->assertForbidden();
    }

    public function test_farmer_cannot_use_admin_routes(): void
    {
        $farmer = User::factory()->create();

        $this->actingAs($farmer, 'sanctum')->getJson('/api/v1/admin/crops')->assertForbidden();
        $this->actingAs($farmer, 'sanctum')->getJson('/api/admin/products')->assertForbidden();
    }

    public function test_pending_account_is_blocked(): void
    {
        $pending = User::factory()->create(['status' => UserStatus::Pending]);

        $this->actingAs($pending, 'sanctum')->getJson('/api/v1/farmer/farms')->assertForbidden();
    }
}
