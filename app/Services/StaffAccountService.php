<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Laboratory;
use App\Models\Pharmacy;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/** إنشاء حسابات موظفي الصيدليات والمخابر من قبل الأدمن فقط */
class StaffAccountService
{
    public function __construct(
        protected RoleProfileService $roleProfiles
    ) {}

    public function createPharmacyStaff(Pharmacy $pharmacy, array $data): User
    {
        return $this->create(UserRole::PharmacyStaff, $data, [
            'pharmacy_id' => $pharmacy->id,
            'job_title'   => $data['job_title'],
        ]);
    }

    public function createLabEmployee(Laboratory $laboratory, array $data): User
    {
        return $this->create(UserRole::LabEmployee, $data, [
            'laboratory_id' => $laboratory->id,
            'job_title'     => $data['job_title'],
        ]);
    }

    private function create(UserRole $role, array $data, array $profileData): User
    {
        return DB::transaction(function () use ($role, $data, $profileData) {
            $user = new User([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            // الأدمن هو من أنشأ الحساب، فهو مفعّل ومعتمد مباشرة
            $user->role              = $role;
            $user->status            = UserStatus::Active;
            $user->email_verified_at = now();
            $user->save();

            $this->roleProfiles->create($user, $role, $profileData);

            return $user;
        });
    }
}
