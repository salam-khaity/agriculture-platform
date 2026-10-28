<?php

namespace App\Models;

use App\Enums\ProductionStatus;
use App\Enums\Season;
use App\Models\Concerns\HasMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlantProduction extends Model
{
    /** @use HasFactory<\Database\Factories\PlantProductionFactory> */

    use HasFactory, SoftDeletes, HasMedia;

    protected $fillable = [
        'field_id', 'crop_id', 'crop_variety_id', 'growth_stage_id',
        'planted_area', 'planting_date', 'season', 'season_year',
        'expected_harvest_date', 'actual_harvest_date', 'yield_quantity',
        'status', 'failure_reason',
    ];

    protected function casts(): array
    {
        return [
            'planted_area'          => 'float',
            'yield_quantity'        => 'float',
            'planting_date'         => 'date',
            'expected_harvest_date' => 'date',
            'actual_harvest_date'   => 'date',
            'season'                => Season::class,
            'status'                => ProductionStatus::class,
        ];
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function crop(): BelongsTo
    {
        return $this->belongsTo(Crop::class)->withTrashed();
    }

    public function variety(): BelongsTo
    {
        return $this->belongsTo(CropVariety::class, 'crop_variety_id')->withTrashed();
    }

    public function growthStage(): BelongsTo
    {
        return $this->belongsTo(GrowthStage::class)->withTrashed();
    }

    public function stageHistory(): HasMany
    {
        return $this->hasMany(PlantProductionStageHistory::class)->orderBy('changed_at')->orderBy('id');
    }

    public function mediaOwnerId(): int
    {
        return $this->field->farm->user_id;
    }


}
