<?php

namespace App\Http\Requests\Region;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRegionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $region = $this->route('region');

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('regions', 'name')->ignore($region),
            ],
            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ];
    }
}
