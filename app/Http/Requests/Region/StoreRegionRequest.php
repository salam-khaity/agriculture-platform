<?php

namespace App\Http\Requests\Region;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRegionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('regions', 'name'),
            ],
            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ];
    }
}
