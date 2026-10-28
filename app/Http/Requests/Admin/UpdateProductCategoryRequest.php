<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // الصلاحيات تُدار عبر middleware في الـ routes
    }

    public function rules(): array
    {
        $id = $this->route('product_category')->id;

        return [
            'name'        => ['sometimes', 'string', 'max:255', Rule::unique('product_categories', 'name')->ignore($id)],
            'type'        => ['sometimes', 'in:plant,animal,general'],
            'description' => ['nullable', 'string'],
            'is_active'   => ['sometimes', 'boolean'],
        ];
    }
}
