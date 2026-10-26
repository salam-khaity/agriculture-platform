<?php

namespace Tests\Feature\Admin;

use App\Models\Crop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CropCatalogTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_create_crop(): void
    {
        $this->actingAs($this->admin(), 'sanctum')
            ->postJson('/api/v1/admin/crops', ['name' => 'Tomato', 'category' => 'vegetable'])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Tomato')
            ->assertJsonPath('message', 'Crop created successfully.');
    }

    public function test_non_admin_cannot_create_crop(): void
    {
        $farmer = User::factory()->create(['role' => 'farmer']);

        $this->actingAs($farmer, 'sanctum')
            ->postJson('/api/v1/admin/crops', ['name' => 'Tomato', 'category' => 'vegetable'])
            ->assertForbidden();
    }

    public function test_crop_name_must_be_unique(): void
    {
        Crop::factory()->create(['name' => 'Tomato']);

        $this->actingAs($this->admin(), 'sanctum')
            ->postJson('/api/v1/admin/crops', ['name' => 'Tomato', 'category' => 'vegetable'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');
    }

    public function test_growth_stage_without_order_is_appended_last(): void
    {
        $crop = Crop::factory()->create();
        $admin = $this->admin();

        $this->actingAs($admin, 'sanctum')
            ->postJson("/api/v1/admin/crops/{$crop->id}/growth-stages", ['name' => 'Germination'])
            ->assertCreated()
            ->assertJsonPath('data.sort_order', 1);

        $this->actingAs($admin, 'sanctum')
            ->postJson("/api/v1/admin/crops/{$crop->id}/growth-stages", ['name' => 'Flowering'])
            ->assertJsonPath('data.sort_order', 2);
    }

    public function test_farmer_sees_only_active_crops(): void
    {
        Crop::factory()->create(['name' => 'Visible', 'is_active' => true]);
        Crop::factory()->create(['name' => 'Hidden', 'is_active' => false]);
        $farmer = User::factory()->create(['role' => 'farmer']);

        $this->actingAs($farmer, 'sanctum')
            ->getJson('/api/v1/farmer/crops')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Visible');
    }

    public function test_inserting_a_stage_in_the_middle_shifts_the_following_ones(): void
    {
        $crop  = Crop::factory()->create();
        $admin = $this->admin();
        $url   = "/api/v1/admin/crops/{$crop->id}/growth-stages";

        foreach (['Germination', 'Flowering', 'Harvest'] as $name) {
            $this->actingAs($admin, 'sanctum')->postJson($url, ['name' => $name])->assertCreated();
        }

        $this->actingAs($admin, 'sanctum')
            ->postJson($url, ['name' => 'Fruit Set', 'sort_order' => 2])
            ->assertCreated()
            ->assertJsonPath('data.sort_order', 2);

        $names = $this->actingAs($admin, 'sanctum')->getJson($url)->json('data.*.name');

        $this->assertSame(['Germination', 'Fruit Set', 'Flowering', 'Harvest'], $names);
    }

    public function test_deleting_a_stage_closes_the_gap(): void
    {
        $crop  = Crop::factory()->create();
        $admin = $this->admin();
        $url   = "/api/v1/admin/crops/{$crop->id}/growth-stages";

        foreach (['A', 'B', 'C'] as $name) {
            $this->actingAs($admin, 'sanctum')->postJson($url, ['name' => $name]);
        }

        $b = $crop->growthStages()->where('name', 'B')->first();
        $this->actingAs($admin, 'sanctum')->deleteJson("/api/v1/admin/growth-stages/{$b->id}")->assertOk();

        $this->assertSame(
            [1, 2],
            $crop->growthStages()->ordered()->pluck('sort_order')->all()
        );
    }
}
