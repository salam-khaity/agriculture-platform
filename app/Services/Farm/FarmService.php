<?php

namespace App\Services\Farm;

use App\Models\Farm;
use App\Models\PlantProduction;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FarmService
{
    public function listForUser(User $user, ?int $perPage = null): Collection|LengthAwarePaginator
    {
        $query = Farm::query()->ownedBy($user->id)->latest();

        return $perPage
            ? $query->paginate($perPage)
            : $query->get();
    }

    public function create(User $user, array $data): Farm
    {
        return $user->farms()->create($data);
    }

    public function update(Farm $farm, array $data): Farm
    {
        if (isset($data['area'])) {
            $usedArea = (float) $farm->fields()->sum('area');

            if (round((float) $data['area'], 2) < round($usedArea, 2)) {
                throw ValidationException::withMessages([
                    'area' => "Farm area cannot be less than the total area of its fields ({$usedArea}).",
                ]);
            }
        }

        $farm->update($data);

        return $farm->refresh();
    }

    public function delete(Farm $farm): void
    {
        DB::transaction(function () use ($farm) {
            PlantProduction::query()->whereIn('field_id', $farm->fields()->select('id'))->delete();
            $farm->fields()->delete();
            $farm->delete();
        });
    }
}
