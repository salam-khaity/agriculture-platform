<?php

namespace App\Models;

use App\Enums\SoilType;
use App\Models\Concerns\HasMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Field extends Model
{
    /** @use HasFactory<\Database\Factories\FieldFactory> */

    use HasFactory, SoftDeletes, HasMedia;

    protected $fillable = [
        'farm_id',
        'name',
        'area',
        'soil_type',
    ];

    protected function casts(): array
    {
        return [
            'area'      => 'float',
            'soil_type' => SoilType::class,
        ];
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function mediaOwnerId(): int
    {
        return $this->farm->user_id;
    }

    public function plantProductions(): HasMany
    {
        return $this->hasMany(PlantProduction::class);
    }

}
