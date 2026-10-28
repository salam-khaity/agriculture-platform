<?php

namespace App\Services;

use App\Models\Laboratory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class LaboratoryService
{
    public function index(array $filters = []): LengthAwarePaginator
    {
        return Laboratory::query()
            ->with('address.region')
            ->when(
                !empty($filters['search']),
                function (Builder $query) use ($filters) {
                    $search = $filters['search'];

                    $query->where(function (Builder $query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('manager_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
                }
            )
            ->when(
                array_key_exists('is_active', $filters)
                && $filters['is_active'] !== '',
                fn (Builder $query) =>
                $query->where('is_active', $filters['is_active'])
            )
            ->latest('id')
            ->paginate(
                min(max((int) ($filters['per_page'] ?? 15), 1), 100)
            );
    }

    public function show(Laboratory $laboratory): Laboratory
    {
        return $laboratory->load('address.region');
    }

    public function store(array $data): Laboratory
    {
        $laboratory = Laboratory::create($data);

        return $laboratory->load('address.region');
    }

    public function update(
        Laboratory $laboratory,
        array $data
    ): Laboratory {
        $laboratory->update($data);

        return $laboratory->refresh()->load('address.region');
    }

    public function destroy(Laboratory $laboratory): void
    {
        $laboratory->delete();
    }
}
