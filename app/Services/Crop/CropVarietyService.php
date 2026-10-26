<?php

namespace App\Services\Crop;

use App\Models\Crop;
use App\Models\CropVariety;
use Illuminate\Database\Eloquent\Collection;

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
        $variety->delete();
    }
}
