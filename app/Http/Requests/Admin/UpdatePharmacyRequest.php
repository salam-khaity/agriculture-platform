<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePharmacyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $pharmacy = $this->route('pharmacy');
        $pharmacyId = $pharmacy instanceof \App\Models\Pharmacy
            ? $pharmacy->id
            : $pharmacy;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'owner_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'nullable',
                'email',
                'max:255',
                Rule::unique('pharmacies', 'email')->ignore($pharmacyId),
            ],
            'description' => ['sometimes', 'nullable', 'string'],
            'address_id' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:addresses,id',
            ],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
