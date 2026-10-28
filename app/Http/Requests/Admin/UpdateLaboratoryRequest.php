<?php

namespace App\Http\Requests\Admin;

use App\Models\Laboratory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLaboratoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $laboratory = $this->route('laboratory');
        $laboratoryId = $laboratory instanceof Laboratory
            ? $laboratory->id
            : $laboratory;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'address_id' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:addresses,id',
            ],
            'manager_name' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],
            'email' => [
                'sometimes',
                'nullable',
                'email',
                'max:255',
                Rule::unique('laboratories', 'email')->ignore($laboratoryId),
            ],
            'description' => ['sometimes', 'nullable', 'string'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
