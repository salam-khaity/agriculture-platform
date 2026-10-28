<?php

namespace App\Services;

use App\Models\Region;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class RegionService
{
    public function index(array $filters = []): LengthAwarePaginator
    {
        return Region::query()
            ->when(
                !empty($filters['search']),
                fn (Builder $query) => $query->where(
                    'name',
                    'like',
                    '%' . $filters['search'] . '%'
                )
            )
            ->when(
                array_key_exists('is_active', $filters)
                && $filters['is_active'] !== null
                && $filters['is_active'] !== '',
                fn (Builder $query) => $query->where(
                    'is_active',
                    filter_var(
                        $filters['is_active'],
                        FILTER_VALIDATE_BOOLEAN
                    )
                )
            )
            ->orderBy('name')
            ->paginate(
                min(max((int) ($filters['per_page'] ?? 15), 1), 100)
            );
    }

    public function store(array $data): Region
    {
        $data['is_active'] ??= true;

        return Region::create($data);
    }

    public function show(Region $region): Region
    {
        return $region;
    }

    public function update(Region $region, array $data): Region
    {
        $region->update($data);

        return $region->refresh();
    }

    public function destroy(Region $region): void
    {
        if ($region->addresses()->exists()) {
            throw ValidationException::withMessages([
                'region' => [
                    'لا يمكن حذف المنطقة لأنها مرتبطة بعناوين. عطّليها بدلاً من حذفها.',
                ],
            ]);
        }

        $region->delete();
    }
}
