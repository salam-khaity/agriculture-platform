<?php

namespace App\Http\Requests\Auth;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /** حقول الأدوار: أي حقل منها غير مسموح للدور المستنتج يُرفض */
    private const ROLE_FIELDS = [
        'specialization',
        'production_types',
        'work_regions',
        'bio',
        'experience_years',
        'job_title',
        'pharmacy_id',
        'laboratory_id',
        'vehicle_type',
        'vehicle_number',
    ];

    /** حقول ممنوع العميل يرسلها نهائياً */
    private const FORBIDDEN_FIELDS = [
        'role', 'status', 'is_admin', 'is_super_admin',
        'email_verified_at', 'banned_at', 'ban_reason',
    ];

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => Str::lower(trim((string) $this->input('email'))),
        ]);
    }

    /** الدور يُستنتج من السيرفر حسب الحقول المرسلة. الافتراضي: farmer */

    public function resolvedRole(): UserRole
    {
        return match (true) {
            $this->has('specialization'),
            $this->has('production_types') => UserRole::AgriculturalSpecialist,

            $this->has('laboratory_id'),
            $this->has('job_title') => UserRole::LabEmployee,

            $this->has('pharmacy_id') => UserRole::PharmacyStaff,

            $this->has('work_regions') => UserRole::DeliveryAgent,

            default => UserRole::Farmer,
        };
    }


    public function rules(): array
    {
        $rules = [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)->letters()->numbers()],
        ];

        foreach (self::FORBIDDEN_FIELDS as $field) {
            $rules[$field] = ['prohibited'];
        }

        $roleRules = $this->roleRules($this->resolvedRole());

        $allowed = collect(array_keys($roleRules))
            ->map(fn ($key) => Str::before($key, '.'))
            ->unique();

        foreach (self::ROLE_FIELDS as $field) {
            if (!$allowed->contains($field)) {
                $rules[$field] = ['prohibited'];
            }
        }

        return $rules + $roleRules;
    }

    private function roleRules(UserRole $role): array
    {
        $activeId = fn (string $table) => Rule::exists($table, 'id')->where('is_active', true);

        return match ($role) {
            UserRole::AgriculturalSpecialist => [
                'specialization'     => ['required', 'integer', $activeId('specializations')],
                'production_types'   => ['required', 'array', 'min:1'],
                'production_types.*' => ['integer', 'distinct', $activeId('production_types')],
                'work_regions'       => ['required', 'array', 'min:1'],
                'work_regions.*'     => ['integer', 'distinct', $activeId('regions')],
                'bio' => ['nullable', 'string', 'max:5000'],
                'experience_years' => ['nullable', 'integer', 'min:0', 'max:80'],
            ],

            UserRole::DeliveryAgent => [
                'vehicle_type' => ['nullable', 'string', 'max:100'],
                'vehicle_number' => ['nullable', 'string', 'max:100'],
                'work_regions' => ['required', 'array', 'min:1'],
                'work_regions.*' => [
                    'required',
                    'integer',
                    'exists:regions,id',
                ],
            ],

            UserRole::LabEmployee => [
                'laboratory_id' => [
                    'required',
                    'integer',
                    Rule::exists('laboratories', 'id')
                        ->where('is_active', true),
                ],
                'job_title' => ['required', 'string', 'max:100'],
            ],

            default => [], // Farmer
        };
    }
}
