<?php

namespace App\Http\Requests\Farmer\Field;

use App\Enums\SoilType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFieldRequest extends FormRequest
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
        $field = $this->route('field');

        return [
            'name' => [
                'sometimes', 'string', 'max:255',
                Rule::unique('fields', 'name')
                    ->where('farm_id', $field->farm_id)
                    ->ignore($field)
                    ->withoutTrashed(),
            ],
            'area'      => ['sometimes', 'numeric', 'gt:0', 'max:99999999'],
            'soil_type' => ['nullable', Rule::enum(SoilType::class)],

        ];
    }
}
