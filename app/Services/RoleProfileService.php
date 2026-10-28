<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\DeliveryAgentProfile;
use App\Models\LabEmployeeProfile;
use App\Models\PharmacyStaffProfile;
use App\Models\SpecialistProfile;
use App\Models\User;
use DomainException;

/**
 * مسؤول فقط عن إنشاء البيانات الخاصة بكل دور بعد إنشاء الـ User.
 * يُستدعى من داخل DB::transaction في AuthService::register
 */
class RoleProfileService
{
    public function create(User $user, UserRole $role, array $data): void
    {
        match ($role) {
            UserRole::Farmer                 => null, // لا بيانات إضافية، والمزرعة لا تُنشأ أثناء التسجيل
            UserRole::AgriculturalSpecialist => $this->specialist($user, $data),
            UserRole::PharmacyStaff          => $this->pharmacyStaff($user, $data),
            UserRole::DeliveryAgent          => $this->deliveryAgent($user, $data),
            UserRole::LabEmployee            => $this->labEmployee($user, $data),
            default => throw new DomainException("Role {$role->value} cannot self-register"),
        };
    }

    private function specialist(User $user, array $data): void
    {
        $profile = SpecialistProfile::create([
            'user_id' => $user->id,
            'specialization_id' => $data['specialization'],
            'bio' => $data['bio'] ?? null,
            'experience_years' => $data['experience_years'] ?? null,
        ]);

        $profile->productionTypes()->sync($data['production_types']);
        $profile->regions()->sync($data['work_regions']);
    }

    private function pharmacyStaff(User $user, array $data): void
    {
        PharmacyStaffProfile::create([
            'user_id'     => $user->id,
            'pharmacy_id' => $data['pharmacy_id'],
            'job_title'   => $data['job_title'],
        ]);
    }

    private function deliveryAgent(User $user, array $data): void
    {
        $profile = DeliveryAgentProfile::create([
            'user_id' => $user->id,
            'vehicle_type' => $data['vehicle_type'] ?? null,
            'vehicle_number' => $data['vehicle_number'] ?? null,
        ]);

        $profile->regions()->sync($data['work_regions'] ?? []);
    }

    private function labEmployee(User $user, array $data): void
    {
        LabEmployeeProfile::create([
            'user_id'       => $user->id,
            'laboratory_id' => $data['laboratory_id'],
            'job_title'     => $data['job_title'],
        ]);
    }
}
