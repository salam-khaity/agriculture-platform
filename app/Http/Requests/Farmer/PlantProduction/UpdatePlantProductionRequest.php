<?php

namespace App\Http\Requests\Farmer\PlantProduction;

use App\Enums\Season;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePlantProductionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $production = $this->route('plant_production');

        $plantingDate = $this->input('planting_date') ?? $production->planting_date->toDateString();

        return [
            'crop_variety_id' => [
                'nullable', 'integer',
                Rule::exists('crop_varieties', 'id')->where('crop_id', $production->crop_id)->whereNull('deleted_at'),
            ],
            'planted_area'          => ['sometimes', 'numeric', 'gt:0', 'max:' . $production->field->area],
            'planting_date'         => ['sometimes', 'date'],
            'expected_harvest_date' => ['nullable', 'date', 'after_or_equal:' . $plantingDate],
            'season'                => ['sometimes', Rule::enum(Season::class)],
            'season_year'           => ['sometimes', 'integer', 'between:2000,2100'],

        ];
    }
}
