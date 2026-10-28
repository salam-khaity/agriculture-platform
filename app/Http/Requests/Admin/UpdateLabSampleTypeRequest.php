<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLabSampleTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // الصلاحيات تُدار عبر middleware في الـ routes
    }

    public function rules(): array
    {
        $id = $this->route('sample_type')->id;

        return [
            'name'        => ['sometimes', 'string', 'max:255', Rule::unique('lab_sample_types', 'name')->ignore($id)],
            'description' => ['nullable', 'string'],
            'is_active'   => ['sometimes', 'boolean'],
        ];
    }
}
