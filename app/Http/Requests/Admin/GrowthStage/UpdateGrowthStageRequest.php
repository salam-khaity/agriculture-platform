<?php

namespace App\Http\Requests\Admin\GrowthStage;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGrowthStageRequest extends FormRequest
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
        $stage = $this->route('growth_stage');

        return [
            'name' => [
                'sometimes', 'string', 'max:255',
                Rule::unique('growth_stages', 'name')
                    ->where('crop_id', $stage->crop_id)->ignore($stage)->withoutTrashed(),
            ],
            'sort_order' => [
                'sometimes', 'integer', 'min:1', 'max:100',
            ],
        ];
    }
}
