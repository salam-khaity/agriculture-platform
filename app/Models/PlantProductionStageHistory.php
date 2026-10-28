<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlantProductionStageHistory extends Model
{
    protected $fillable = ['growth_stage_id', 'changed_by', 'changed_at'];

    protected function casts(): array
    {
        return ['changed_at' => 'datetime'];
    }

    public function growthStage(): BelongsTo
    {
        return $this->belongsTo(GrowthStage::class)->withTrashed();
    }
}
