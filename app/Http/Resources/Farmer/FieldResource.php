<?php

namespace App\Http\Resources\Farmer;

use App\Http\Resources\Media\MediaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FieldResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'farm_id'   => $this->farm_id,
            'name'      => $this->name,
            'area'      => $this->area,
            'soil_type' => $this->soil_type?->value,
            'created_at' => $this->created_at?->toIso8601String(),
            'media' => MediaResource::collection($this->whenLoaded('media')),
        ];
    }
}
