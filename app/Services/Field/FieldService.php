<?php

namespace App\Services\Field;

use App\Models\Farm;
use App\Models\Field;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FieldService
{
    public function listForFarm(Farm $farm): Collection
    {
        return $farm->fields()->latest()->get();
    }

    public function create(Farm $farm, array $data): Field
    {
        return DB::transaction(function () use ($farm, $data) {

            $farm = Farm::query()->lockForUpdate()->findOrFail($farm->id);

            $this->ensureAreaFits($farm, (float) $data['area']);

            return $farm->fields()->create($data);
        });
    }

    public function update(Field $field, array $data): Field
    {
        return DB::transaction(function () use ($field, $data) {
            if (isset($data['area'])) {
                $farm = Farm::query()->lockForUpdate()->findOrFail($field->farm_id);

                $this->ensureAreaFits($farm, (float) $data['area'], $field->id);
            }

            $field->update($data);

            return $field->refresh();
        });
    }

    public function delete(Field $field): void
    {
        $field->delete(); // Soft Delete
    }

    // قاعدة العمل: مجموع مساحات الحقول لا يتجاوز مساحة المزرعة
    private function ensureAreaFits(Farm $farm, float $newArea, ?int $ignoreFieldId = null): void
    {
        $usedArea = (float) $farm->fields()
            ->when($ignoreFieldId, fn ($query) => $query->whereKeyNot($ignoreFieldId))
            ->sum('area');

        $available = round($farm->area - $usedArea, 2);

        if (round($newArea, 2) > $available) {
            throw ValidationException::withMessages([
                'area' => "Field area exceeds the available farm area ({$available}).",
            ]);
        }
    }
}
