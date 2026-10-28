<?php

namespace App\Http\Resources\Farmer;

use App\Http\Resources\Crop\CropResource;
use App\Http\Resources\Crop\CropVarietyResource;
use App\Http\Resources\Crop\GrowthStageResource;
use App\Http\Resources\Media\MediaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlantProductionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'field_id'              => $this->field_id,
            'status'                => $this->status->value,
            'season'                => $this->season->value,
            'season_year'           => $this->season_year,
            'planting_date'         => $this->planting_date?->toDateString(),
            'expected_harvest_date' => $this->expected_harvest_date?->toDateString(),
            'actual_harvest_date'   => $this->actual_harvest_date?->toDateString(),
            'planted_area'          => $this->planted_area,
            'yield_quantity'        => $this->yield_quantity,
            'failure_reason'        => $this->failure_reason,

            'crop'         => $this->whenLoaded('crop', fn () => new CropResource($this->crop)),
            'variety'      => $this->whenLoaded('variety', fn () => $this->variety ? new CropVarietyResource($this->variety) : null),
            'growth_stage' => $this->whenLoaded('growthStage', fn () => new GrowthStageResource($this->growthStage)),

            'stage_history' => $this->whenLoaded('stageHistory', fn () => $this->stageHistory->map(fn ($h) => [
                'growth_stage_id' => $h->growth_stage_id,
                'name'            => $h->growthStage?->name,
                'changed_at'      => $h->changed_at?->toIso8601String(),
            ])),

            'media'      => MediaResource::collection($this->whenLoaded('media')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
