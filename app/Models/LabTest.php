<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabTest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'lab_sample_type_id', 'name', 'unit', 'method',
        'reference_min', 'reference_max', 'reference_text',
        'price', 'is_active',
    ];

    protected $casts = [
        'price'     => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function sampleType(): BelongsTo
    {
        return $this->belongsTo(LabSampleType::class, 'lab_sample_type_id');
    }
}
