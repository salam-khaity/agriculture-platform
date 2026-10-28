<?php

namespace App\Http\Requests\Farmer\PlantProduction;

use App\Enums\Season;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePlantProductionRequest extends FormRequest
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
        $field  = $this->route('field');
        $cropId = $this->input('crop_id');

        return [
            'crop_id' => [
                'required', 'integer',
                Rule::exists('crops', 'id')->where('is_active', true)->whereNull('deleted_at'),
            ],
             'crop_variety_id' => [
                'nullable', 'integer',
                Rule::exists('crop_varieties', 'id')->where('crop_id', $cropId)->whereNull('deleted_at'),
            ],
            'growth_stage_id' => [
                'sometimes', 'integer',
                Rule::exists('growth_stages', 'id')->where('crop_id', $cropId)->whereNull('deleted_at'),
            ],
            'planted_area'          => ['sometimes', 'numeric', 'gt:0', 'max:' . $field->area],
            'planting_date'         => ['required', 'date'],
            'expected_harvest_date' => ['nullable', 'date', 'after_or_equal:planting_date'],
            'season'                => ['required', Rule::enum(Season::class)],
            'season_year'           => ['sometimes', 'integer', 'between:2000,2100'],

        ];
    }
}
