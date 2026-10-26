<?php

namespace App\Http\Requests\Admin\Crop;

use App\Enums\CropCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCropRequest extends FormRequest
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
        return [
            'name'            => ['required', 'string', 'max:255', Rule::unique('crops', 'name')->withoutTrashed()],
            'scientific_name' => ['nullable', 'string', 'max:255'],
            'category'        => ['required', Rule::enum(CropCategory::class)],
            'description'     => ['nullable', 'string', 'max:2000'],
            'is_active'       => ['sometimes', 'boolean'],
        ];
    }
}
