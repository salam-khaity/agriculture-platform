<?php

namespace App\Http\Resources\Farmer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FarmResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'   => $this->id,
            'name' => $this->name,
            'activity_type' => $this->activity_type->value,
            'location' => [
                'latitude'    => $this->latitude,
                'longitude'   => $this->longitude,
                'description' => $this->location_description,
            ],
            'area'       => $this->area,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
