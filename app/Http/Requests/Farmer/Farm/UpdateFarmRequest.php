<?php

namespace App\Http\Requests\Farmer\Farm;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFarmRequest extends FormRequest
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
            'name' => [
                'sometimes', 'string', 'max:255',
                Rule::unique('farms', 'name')
                    ->where('user_id', $this->user()->id)
                    ->ignore($this->route('farm'))
                    ->withoutTrashed(),
            ],
            'latitude'             => ['sometimes', 'numeric', 'between:-90,90'],
            'longitude'            => ['sometimes', 'numeric', 'between:-180,180'],
            'area'                 => ['sometimes', 'numeric', 'gt:0', 'max:99999999'],
            'location_description' => ['nullable', 'string', 'max:500'],

        ];
    }
}
