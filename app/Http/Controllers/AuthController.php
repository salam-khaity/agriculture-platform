<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResendOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $service
    ) {}

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        // الدور يُحقن من السيرفر فقط بعد نجاح الـ validation
        $data['role'] = $request->resolvedRole()->value;

        return $this->respond($this->service->register($data));
    }

    public function resendOtp(ResendOtpRequest $request)
    {
        return $this->respond(
            $this->service->resendOtp($request->email)
        );
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {
        return $this->respond(
            $this->service->verifyOtp($request->email, $request->otp)
        );
    }

    // Step 1: Login request -> send OTP (أو مباشرة للـ Admin)
    public function login(LoginRequest $request)
    {
        return $this->respond(
            $this->service->login($request->email, $request->password)
        );
    }

    public function logout(Request $request)
    {
        return $this->respond(
            $this->service->logout($request->user())
        );
    }

    /**
     * الـ HTTP status الحقيقي = $result['status'] (الـ body ما تغيّر).
     */
    private function respond(array $result): JsonResponse
    {
        return response()->json($result, $result['status'] ?? 200);
    }
}
