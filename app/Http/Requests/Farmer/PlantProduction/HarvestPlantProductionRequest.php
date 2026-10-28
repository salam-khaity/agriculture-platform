<?php

namespace App\Http\Requests\Farmer\PlantProduction;

use Illuminate\Foundation\Http\FormRequest;

class HarvestPlantProductionRequest extends FormRequest
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

        return [
            'actual_harvest_date' => [
                'required', 'date',
                'after_or_equal:' . $production->planting_date->toDateString(),
                'before_or_equal:today',
            ],
            'yield_quantity' => ['required', 'numeric', 'min:0', 'max:9999999999'], // kg
        ];
    }
}
