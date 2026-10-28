<?php

namespace App\Services;

use App\Models\Pharmacy;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class PharmacyService
{
    public function index(array $filters = []): LengthAwarePaginator
    {
        return Pharmacy::query()
            ->with('address.region')
            ->when(
                !empty($filters['search']),
                function (Builder $query) use ($filters) {
                    $search = $filters['search'];

                    $query->where(function (Builder $query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('owner_name', 'like', "%{$search}%")
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

    public function show(Pharmacy $pharmacy): Pharmacy
    {
        return $pharmacy->load('address.region');
    }

    public function store(array $data): Pharmacy
    {
        $pharmacy = Pharmacy::create($data);

        return $pharmacy->load('address.region');
    }

    public function update(Pharmacy $pharmacy, array $data): Pharmacy
    {
        $pharmacy->update($data);

        return $pharmacy->refresh()->load('address.region');
    }

    public function destroy(Pharmacy $pharmacy): void
    {
        $pharmacy->delete();
    }
}
