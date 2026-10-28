<?php

namespace App\Services;

use App\Models\LabSampleType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class LabSampleTypeService extends BaseCrudService
{
    protected string $model = LabSampleType::class;

    public function delete(Model $model): void
    {
        if ($model->tests()->withTrashed()->exists()) {
            throw ValidationException::withMessages([
                'sample_type' => 'لا يمكن حذف نوع العينة لأنه مرتبط باختبارات.',
            ]);
        }

        parent::delete($model);
    }
}
