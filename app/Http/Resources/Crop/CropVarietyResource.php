<?php

namespace App\Http\Resources\Crop;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CropVarietyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
        'id'      => $this->id,
        'crop_id' => $this->crop_id,
        'name'    => $this->name,
    ];
    }
}
