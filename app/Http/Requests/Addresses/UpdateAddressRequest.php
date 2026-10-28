<?php

namespace App\Http\Requests\Addresses;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'label' => ['sometimes', 'nullable', 'string', 'max:100'],
            'address_line' => ['sometimes', 'required', 'string', 'max:500'],
            'region_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:regions,id',
            ],
            'latitude' => [
                'sometimes',
                'nullable',
                'numeric',
                'between:-90,90',
            ],
            'longitude' => [
                'sometimes',
                'nullable',
                'numeric',
                'between:-180,180',
            ],
        ];
    }
}
