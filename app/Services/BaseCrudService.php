<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class BaseCrudService
{
    /** @var class-string<Model> */
    protected string $model;

    protected array $with = [];

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model::query()->with($this->with);

        $this->applyFilters($query, $filters);

        return $query->latest('id')->paginate($perPage);
    }

    public function find(Model $model): Model
    {
        return $model->load($this->with);
    }

    public function create(array $data): Model
    {
        return $this->model::create($data)->load($this->with);
    }

    public function update(Model $model, array $data): Model
    {
        $model->update($data);

        return $model->refresh()->load($this->with);
    }

    public function delete(Model $model): void
    {
        $model->delete();
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        $query->when(
            $filters['search'] ?? null,
            fn (Builder $q, string $search) => $q->where('name', 'like', "%{$search}%")
        );

        $query->when(
            isset($filters['is_active']),
            fn (Builder $q) => $q->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN))
        );
    }
}
