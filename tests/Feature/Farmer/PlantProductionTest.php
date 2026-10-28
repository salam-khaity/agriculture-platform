<?php

namespace Tests\Feature\Farmer;

use App\Models\Crop;
use App\Models\CropVariety;
use App\Models\Farm;
use App\Models\Field;
use App\Models\GrowthStage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlantProductionTest extends TestCase
{
    use RefreshDatabase;

       private function prepare(): array
    {
        $user  = User::factory()->create();
        $farm  = Farm::factory()->create(['user_id' => $user->id, 'area' => 50]);
        $field = Field::factory()->create(['farm_id' => $farm->id, 'area' => 10]);
        $crop  = Crop::factory()->create();

        $stages = [
            GrowthStage::factory()->create(['crop_id' => $crop->id, 'sort_order' => 1]),
            GrowthStage::factory()->create(['crop_id' => $crop->id, 'sort_order' => 2]),
        ];

        return compact('user', 'field', 'crop', 'stages');
    }

    private function createProduction(array $ctx, array $extra = [])
    {
        return $this->actingAs($ctx['user'], 'sanctum')
            ->postJson("/api/v1/farmer/fields/{$ctx['field']->id}/plant-productions", array_merge([
                'crop_id'       => $ctx['crop']->id,
                'planting_date' => '2026-03-01',
                'season'        => 'spring',
            ], $extra));
    }

    public function test_farmer_can_create_production_with_first_stage_by_default(): void
    {
        $ctx = $this->prepare();

        $this->createProduction($ctx)
            ->assertCreated()
            ->assertJsonPath('data.status', 'active')
            ->assertJsonPath('data.growth_stage.id', $ctx['stages'][0]->id)
            ->assertJsonPath('message', 'Plant production created successfully.');

        $this->assertDatabaseCount('plant_production_stage_histories', 1);
    }

    public function test_field_cannot_have_two_active_productions(): void
    {
        $ctx = $this->prepare();

        $this->createProduction($ctx)->assertCreated();

        $this->createProduction($ctx)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('field');
    }

    public function test_variety_of_another_crop_is_rejected(): void
    {
        $ctx   = $this->prepare();
        $other = CropVariety::factory()->create();

        $this->createProduction($ctx, ['crop_variety_id' => $other->id])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('crop_variety_id');
    }

    public function test_changing_stage_records_history(): void
    {
        $ctx = $this->prepare();
        $id  = $this->createProduction($ctx)->json('data.id');

        $this->actingAs($ctx['user'], 'sanctum')
            ->postJson("/api/v1/farmer/plant-productions/{$id}/stage", [
                'growth_stage_id' => $ctx['stages'][1]->id,
            ])
            ->assertOk()
            ->assertJsonPath('data.growth_stage.id', $ctx['stages'][1]->id);

        $this->assertDatabaseCount('plant_production_stage_histories', 2);
    }

    public function test_harvest_closes_production_and_allows_a_new_one(): void
    {
        $ctx = $this->prepare();
        $id  = $this->createProduction($ctx)->json('data.id');

        $this->actingAs($ctx['user'], 'sanctum')
            ->postJson("/api/v1/farmer/plant-productions/{$id}/harvest", [
                'actual_harvest_date' => '2026-06-01',
                'yield_quantity'      => 120.5,
            ])
            ->assertOk()
            ->assertJsonPath('data.status', 'harvested');

        $this->createProduction($ctx)->assertCreated();
    }

    public function test_farmer_cannot_add_production_to_another_farmers_field(): void
    {
        $ctx   = $this->prepare();
        $other = User::factory()->create();

        $this->actingAs($other, 'sanctum')
            ->postJson("/api/v1/farmer/fields/{$ctx['field']->id}/plant-productions", [
                'crop_id'       => $ctx['crop']->id,
                'planting_date' => '2026-03-01',
                'season'        => 'spring',
            ])
            ->assertForbidden();
    }

    public function test_crop_used_by_active_production_cannot_be_deleted(): void
    {
        $ctx   = $this->prepare();
        $admin = User::factory()->create(['role' => 'admin']);

        $this->createProduction($ctx)->assertCreated();

        $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/v1/admin/crops/{$ctx['crop']->id}")
            ->assertUnprocessable()
            ->assertJsonValidationErrors('crop');
    }
}
