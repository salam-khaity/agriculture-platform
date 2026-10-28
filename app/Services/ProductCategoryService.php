<?php

namespace App\Services;

use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class ProductCategoryService extends BaseCrudService
{
    protected string $model = ProductCategory::class;

    public function delete(Model $model): void
    {
        if ($model->products()->withTrashed()->exists()) {
            throw ValidationException::withMessages([
                'category' => 'لا يمكن حذف التصنيف لأنه يحتوي على منتجات.',
            ]);
        }

        parent::delete($model);
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        parent::applyFilters($query, $filters);

        $query->when($filters['type'] ?? null, fn (Builder $q, string $type) => $q->where('type', $type));
    }
}
