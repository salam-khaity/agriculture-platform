<?php

namespace App\Services;

use App\Models\LabTest;
use Illuminate\Database\Eloquent\Builder;

class LabTestService extends BaseCrudService
{
    protected string $model = LabTest::class;

    protected array $with = ['sampleType'];

    protected function applyFilters(Builder $query, array $filters): void
    {
        parent::applyFilters($query, $filters);

        $query->when(
            $filters['lab_sample_type_id'] ?? null,
            fn (Builder $q, $id) => $q->where('lab_sample_type_id', $id)
        );
    }
}
