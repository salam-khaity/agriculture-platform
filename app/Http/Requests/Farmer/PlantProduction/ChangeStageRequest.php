<?php

namespace App\Http\Requests\Farmer\PlantProduction;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeStageRequest extends FormRequest
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
            'growth_stage_id' => [
                'required', 'integer',
                Rule::exists('growth_stages', 'id')->where('crop_id', $production->crop_id)->whereNull('deleted_at'),
            ],
        ];
    }
}
