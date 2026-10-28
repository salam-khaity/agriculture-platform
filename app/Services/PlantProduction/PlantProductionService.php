<?php

namespace App\Services\PlantProduction;

use App\Enums\ProductionStatus;
use App\Models\Field;
use App\Models\GrowthStage;
use App\Models\PlantProduction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PlantProductionService
{
    public function listForField(Field $field, ?string $status = null): Collection
    {
        return $field->plantProductions()
            ->with(['crop', 'variety', 'growthStage'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest('planting_date')
            ->latest('id')
            ->get();
    }

    public function create(Field $field, User $user, array $data): PlantProduction
    {
        return DB::transaction(function () use ($field, $user, $data) {

            $field = Field::query()->lockForUpdate()->findOrFail($field->id);

            $hasActive = $field->plantProductions()
                ->where('status', ProductionStatus::Active->value)
                ->exists();

            if ($hasActive) {
                throw ValidationException::withMessages([
                    'field' => 'This field already has an active production.',
                ]);
            }


            $data['growth_stage_id'] ??= $this->firstStageId((int) $data['crop_id']);
            $data['planted_area']    ??= $field->area;
            $data['season_year']     ??= Carbon::parse($data['planting_date'])->year;
            $data['status']            = ProductionStatus::Active->value;

            $production = $field->plantProductions()->create($data);

            $this->recordStage($production, $production->growth_stage_id, $user);

            return $this->loadDetails($production);
        });
    }

    public function update(PlantProduction $production, array $data): PlantProduction
    {
        $this->ensureActive($production);

        $production->update($data);

        return $this->loadDetails($production->refresh());
    }

    public function changeStage(PlantProduction $production, int $stageId, User $user): PlantProduction
    {
        return DB::transaction(function () use ($production, $stageId, $user) {
            $production = PlantProduction::query()->lockForUpdate()->findOrFail($production->id);

            $this->ensureActive($production);

            if ($production->growth_stage_id === $stageId) {
                throw ValidationException::withMessages([
                    'growth_stage_id' => 'The production is already in this growth stage.',
                ]);
            }

            $production->update(['growth_stage_id' => $stageId]);

            $this->recordStage($production, $stageId, $user);

            return $this->loadDetails($production->refresh());
        });
    }

    public function harvest(PlantProduction $production, array $data): PlantProduction
    {
        $this->ensureActive($production);

        $production->update([
            'actual_harvest_date' => $data['actual_harvest_date'],
            'yield_quantity'      => $data['yield_quantity'],
            'status'              => ProductionStatus::Harvested->value,
        ]);

        return $this->loadDetails($production->refresh());
    }

    public function fail(PlantProduction $production, ?string $reason): PlantProduction
    {
        $this->ensureActive($production);

        $production->update([
            'status'         => ProductionStatus::Failed->value,
            'failure_reason' => $reason,
        ]);

        return $this->loadDetails($production->refresh());
    }

    public function delete(PlantProduction $production): void
    {
        $production->delete(); // Soft Delete
    }

    public function loadDetails(PlantProduction $production): PlantProduction
    {
        return $production->load(['crop', 'variety', 'growthStage', 'stageHistory.growthStage', 'media']);
    }

    private function ensureActive(PlantProduction $production): void
    {
        if ($production->status !== ProductionStatus::Active) {
            throw ValidationException::withMessages([
                'status' => 'Only an active production can be modified.',
            ]);
        }
    }

    private function firstStageId(int $cropId): int
    {
        $id = GrowthStage::query()->where('crop_id', $cropId)->orderBy('sort_order')->value('id');

        if (! $id) {
            throw ValidationException::withMessages([
                'crop_id' => 'The selected crop has no growth stages yet.',
            ]);
        }

        return $id;
    }

    private function recordStage(PlantProduction $production, int $stageId, User $user): void
    {
        $production->stageHistory()->create([
            'growth_stage_id' => $stageId,
            'changed_by'      => $user->id,
            'changed_at'      => now(),
        ]);
    }
}
