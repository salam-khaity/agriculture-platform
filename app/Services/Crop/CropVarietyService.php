<?php

namespace App\Services\Crop;

use App\Enums\ProductionStatus;
use App\Models\Crop;
use App\Models\CropVariety;
use App\Models\PlantProduction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class CropVarietyService
{
    public function listForCrop(Crop $crop): Collection
    {
        return $crop->varieties()->orderBy('name')->get();
    }

    public function create(Crop $crop, array $data): CropVariety
    {
        return $crop->varieties()->create($data);
    }

    public function update(CropVariety $variety, array $data): CropVariety
    {
        $variety->update($data);

        return $variety->refresh();
    }

    public function delete(CropVariety $variety): void
    {
        if (PlantProduction::query()
            ->where('crop_variety_id', $variety->id)
            ->where('status', ProductionStatus::Active->value)
            ->exists()) {
            throw ValidationException::withMessages([
                'variety' => 'This variety is used by an active production and cannot be deleted.',
            ]);
        }
        $variety->delete();
    }
}
