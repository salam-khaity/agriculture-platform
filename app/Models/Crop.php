<?php

namespace App\Models;

use App\Enums\CropCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Crop extends Model
{
    /** @use HasFactory<\Database\Factories\CropFactory> */

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'scientific_name', 'category', 'description', 'is_active'
    ];

    protected function casts(): array
    {
        return [
            'category'  => CropCategory::class,
            'is_active' => 'boolean',
        ];
    }

    public function varieties(): HasMany
    {
        return $this->hasMany(CropVariety::class);
    }

    public function growthStages(): HasMany
    {
        return $this->hasMany(GrowthStage::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }


}
