<?php

namespace App\Services\Crop;

use App\Enums\ProductionStatus;
use App\Models\Crop;
use App\Models\PlantProduction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CropService
{
    public function list(?string $search = null, ?int $perPage = null): Collection|LengthAwarePaginator
    {
        $query = Crop::query()
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name');

        return $perPage ? $query->paginate($perPage) : $query->get();
    }

    public function listActiveWithDetails(): Collection
    {
        return $this->withDetails(Crop::query()->active())->orderBy('name')->get();
    }

    public function showForFarmer(Crop $crop): Crop
    {
        if (! $crop->is_active) {
            throw (new ModelNotFoundException())->setModel(Crop::class);
        }

        return $this->loadDetails($crop);
    }

    public function loadDetails(Crop $crop): Crop
    {
        return $crop->load([
            'varieties'    => fn ($q) => $q->orderBy('name'),
            'growthStages' => fn ($q) => $q->ordered(),
        ]);
    }

    public function create(array $data): Crop
    {
        return Crop::create($data);
    }

    public function update(Crop $crop, array $data): Crop
    {
        $crop->update($data);

        return $crop->refresh();
    }

    public function delete(Crop $crop): void
    {
        if (PlantProduction::query()
            ->where('crop_id', $crop->id)
            ->where('status', ProductionStatus::Active->value)
            ->exists()) {
            throw ValidationException::withMessages([
                'crop' => 'This crop is used by an active production and cannot be deleted.',
            ]);
        }
        DB::transaction(function () use ($crop) {
            $crop->varieties()->delete();
            $crop->growthStages()->delete();
            $crop->delete();
        });
    }

    private function withDetails($query)
    {
        return $query->with([
            'varieties'    => fn ($q) => $q->orderBy('name'),
            'growthStages' => fn ($q) => $q->ordered(),
        ]);
    }
}
