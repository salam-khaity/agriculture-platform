<?php

namespace App\Http\Resources\Crop;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CropResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'scientific_name' => $this->scientific_name,
            'category'        => $this->category->value,
            'description'     => $this->description,
            'is_active'       => $this->is_active,
            'varieties'       => CropVarietyResource::collection($this->whenLoaded('varieties')),
            'growth_stages'   => GrowthStageResource::collection($this->whenLoaded('growthStages')),
        ];
    }
}
