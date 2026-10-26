<?php

namespace App\Http\Requests\Farmer\Farm;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFarmRequest extends FormRequest
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
                'required', 'string', 'max:255',
                Rule::unique('farms', 'name')
                    ->where('user_id', $this->user()->id)
                    ->withoutTrashed(),
            ],
            'latitude'             => ['required', 'numeric', 'between:-90,90'],
            'longitude'            => ['required', 'numeric', 'between:-180,180'],
            'area'                 => ['required', 'numeric', 'gt:0', 'max:99999999'],
            'location_description' => ['nullable', 'string', 'max:500'],

        ];
    }
}
