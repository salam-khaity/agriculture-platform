<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLabTestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // الصلاحيات تُدار عبر middleware في الـ routes
    }

    public function rules(): array
    {
        return [
            'lab_sample_type_id' => ['sometimes', 'exists:lab_sample_types,id'],
            'name'               => ['sometimes', 'string', 'max:255'],
            'unit'               => ['nullable', 'string', 'max:50'],
            'method'             => ['nullable', 'string', 'max:255'],
            'reference_min'      => ['nullable', 'numeric'],
            'reference_max'      => ['nullable', 'numeric', 'gte:reference_min'],
            'reference_text'     => ['nullable', 'string', 'max:255'],
            'price'              => ['sometimes', 'numeric', 'min:0'],
            'is_active'          => ['sometimes', 'boolean'],
        ];
    }
}
