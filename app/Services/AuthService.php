<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Enums\UserStatus;
//use App\Models\AccountDeletionSchedule;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(
        protected OtpService $otpService,
        protected RoleProfileService $roleProfiles
    ) {}

    public function register(array $data): array
    {
        // الـ Role جاي من الطلب (بعد التحقق في RegisterRequest)، مو مكتوب بالكود
        $role = UserRole::tryFrom($data['role'] ?? '');

        // Defense in depth: حتى لو تجاوز أحد الـ FormRequest
        if (!$role || !$role->isPublicRegistrable()) {
            return [
                'status'  => 403,
                'message' => 'Not allowed'
            ];
        }

        $user = DB::transaction(function () use ($data, $role) {

            $user = new User([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            // إسناد صريح: role و status ما بيجوا أبداً من mass assignment
            $user->role   = $role;
            $user->status = $role->requiresApproval()
                ? UserStatus::Pending
                : UserStatus::Active;
            $user->save();

            $this->roleProfiles->create($user, $role, $data);

//            AuditService::log(
//                $user,
//                'register',
//                'User',
//                $user->id
//            );

            return $user;
        });

        // OTP بعد الـ commit: إذا فشل الإرسال ما منخسر بيانات المستخدم (يقدر يعمل resend)
        $result = $this->otpService->send('register', $user->email);

        return $result + ['requires_approval' => $role->requiresApproval()];
    }

    // STEP 1: login -> send OTP only (أو مباشرة للـ Admin)
    public function login(string $email, string $password): array
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return [
                'status'  => 401,
                'message' => 'Invalid credentials'
            ];
        }

        if ($user->banned_at) {
            return [
                'status'  => 403,
                'message' => 'Your account has been banned',
                'reason'  => $user->ban_reason,
            ];
        }

        if ($user->role->isAdmin()) {
//            AuditService::log($user, 'login', 'User', $user->id);

            return [
                'status'  => 200,
                'message' => 'Login success',
                'token'   => $user->createToken('auth_token')->plainTextToken,
                'user'    => $this->userPayload($user),
            ];
        }

        if (!$user->email_verified_at) {
            return [
                'status'  => 403,
                'message' => 'Please verify your email first'
            ];
        }

        // ✅ جديد: الحسابات التي تنتظر موافقة الإدارة / المرفوضة / الموقوفة
        if ($user->status !== UserStatus::Active) {
            return [
                'status'  => 403,
                'message' => match ($user->status) {
                    UserStatus::Pending => 'Account waiting for admin approval',
                    UserStatus::Rejected => 'Your registration was rejected',
                    default => 'Your account is suspended',
                },
            ];
        }

        // 🔥 إلغاء جدولة الحذف إذا موجودة
//        $schedule = AccountDeletionSchedule::where('user_id', $user->id)->first();
//
//        if ($schedule) {
//            $schedule->delete();
//
//            return [
//                'status'  => 200,
//                'message' => 'Account restored successfully. You can login now.'
//            ];
//        }

        // ✅ باقي الأدوار — OTP flow
        return $this->otpService->send('login', $email);
    }

    public function logout($user): array
    {
//        AuditService::log($user, 'logout', 'User', $user->id);

        $user->currentAccessToken()->delete();

        return [
            'status'  => 200,
            'message' => 'Logged out'
        ];
    }

    public function resendOtp(string $email): array
    {
        if (Cache::has("otp:login:$email")) {
            return $this->otpService->resend('login', $email);
        }

        if (Cache::has("otp:register:$email")) {
            return $this->otpService->resend('register', $email);
        }

        return [
            'status' => 404,
            'message' => 'No active OTP request found'
        ];
    }

    public function verifyOtp(string $email, string $otp): array
    {
        // أولاً نجرب OTP الخاص بالـ login
        $login = $this->otpService->verify('login', $email, $otp);

        if ($login['status'] === 200) {

            $data = $this->otpService->consume('login', $email);

            if (!$data) {
                return [
                    'status' => 400,
                    'message' => 'OTP expired or invalid session'
                ];
            }

            $user = User::find($data['user_id']);

            if (!$user) {
                return [
                    'status' => 404,
                    'message' => 'User not found'
                ];
            }

//            AuditService::log($user, 'login', 'User', $user->id);

            return [
                'status' => 200,
                'message' => 'Login success',
                'token' => $user->createToken('auth_token')->plainTextToken,
                'user' => $this->userPayload($user),
            ];
        }

        // إذا مو Login نجرب Register
        $register = $this->otpService->verify('register', $email, $otp);

        if ($register['status'] === 200) {

            $user = User::where('email', $email)->first();

            if (!$user) {
                return [
                    'status' => 404,
                    'message' => 'User not found'
                ];
            }

            $user->email_verified_at = now();
            $user->save();

            $user->refresh();
            $this->otpService->consume('register', $email);

//            AuditService::log($user, 'verify_email', 'User', $user->id);
//
            // ✅ جديد: الحساب اللي بحاجة موافقة ما بياخد token قبل التفعيل
            if ($user->status !== UserStatus::Active) {
                return [
                    'status' => 200,
                    'message' => 'Email verified. Your account is waiting for admin approval',
                    'requires_approval' => true,
                ];
            }

            return [
                'status' => 200,
                'message' => 'Verified successfully',
                'token' => $user->createToken('auth_token')->plainTextToken,
                'user' => $this->userPayload($user),
            ];
        }

        // إذا فشل الاثنين
        return $login['message'] !== 'OTP expired or not found'
            ? $login
            : $register;
    }

    private function userPayload(User $user): array
    {
        return [
            'name'  => $user->name,
            'email' => $user->email,
            'role'  => $user->role->value,
        ];
    }
}
