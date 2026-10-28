<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/** مراجعة طلبات التسجيل العام للأدوار المهنية (مهندس زراعي، مندوب توصيل) */
class RegistrationReviewService
{
    public const REVIEWABLE_ROLES = [
        UserRole::AgriculturalSpecialist,
        UserRole::DeliveryAgent,
    ];

    public static function reviewableValues(): array
    {
        return array_map(fn (UserRole $r) => $r->value, self::REVIEWABLE_ROLES);
    }

    public function approve(User $user): array
    {
        return DB::transaction(function () use ($user) {
            $user = User::lockForUpdate()->findOrFail($user->id);

            if (!in_array($user->role, self::REVIEWABLE_ROLES, true)) {
                return $this->fail(422, 'This account is not a registration request');
            }

            // نقبل فقط المعلّق أو المرفوض سابقاً (ما منفعّل حساب موقوف بالغلط)
            if (!in_array($user->status, [UserStatus::Pending, UserStatus::Rejected], true)) {
                return $this->fail(409, 'Only pending or rejected requests can be approved');
            }

            if (!$user->email_verified_at) {
                return $this->fail(422, 'User has not verified their email yet');
            }

            if ($user->banned_at) {
                return $this->fail(422, 'User is banned');
            }

            $user->status = UserStatus::Active;
            $user->save();

            return [
                'status'  => 200,
                'message' => 'Registration approved',
                'user'    => $this->payload($user),
            ];
        });
    }

    public function reject(User $user): array
    {
        return DB::transaction(function () use ($user) {
            $user = User::lockForUpdate()->findOrFail($user->id);

            if (!in_array($user->role, self::REVIEWABLE_ROLES, true)) {
                return $this->fail(422, 'This account is not a registration request');
            }

            if ($user->status !== UserStatus::Pending) {
                return $this->fail(409, 'Only pending requests can be rejected');
            }

            $user->status = UserStatus::Rejected;
            $user->save();

            return [
                'status'  => 200,
                'message' => 'Registration rejected',
                'user'    => $this->payload($user),
            ];
        });
    }

    private function fail(int $status, string $message): array
    {
        return ['status' => $status, 'message' => $message];
    }

    private function payload(User $user): array
    {
        return [
            'id'     => $user->id,
            'name'   => $user->name,
            'email'  => $user->email,
            'role'   => $user->role->value,
            'status' => $user->status->value,
        ];
    }
}
