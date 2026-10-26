<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CropVariety extends Model
{
    /** @use HasFactory<\Database\Factories\CropVarietyFactory> */

    use HasFactory, SoftDeletes;

    protected $fillable = ['crop_id', 'name'];

    public function crop(): BelongsTo
    {
        return $this->belongsTo(Crop::class);
    }
}
