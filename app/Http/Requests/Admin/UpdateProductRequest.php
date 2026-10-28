<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // الصلاحيات تُدار عبر middleware في الـ routes
    }

    public function rules(): array
    {
        return [
            'product_category_id' => ['sometimes', 'exists:product_categories,id'],
            'name'                => ['sometimes', 'string', 'max:255'],
            'description'         => ['nullable', 'string'],
            'price'               => ['sometimes', 'numeric', 'min:0'],
            'stock_quantity'      => ['sometimes', 'integer', 'min:0'],
            'min_stock_level'     => ['sometimes', 'integer', 'min:0'],
            'usage_instructions'  => ['nullable', 'string'],
            'warnings'            => ['nullable', 'string'],
            'image'               => ['nullable', 'image', 'max:2048'],
            'is_active'           => ['sometimes', 'boolean'],
        ];
    }
}
