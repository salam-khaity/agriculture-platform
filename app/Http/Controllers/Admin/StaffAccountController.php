<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStaffRequest;
use App\Models\Laboratory;
use App\Models\Pharmacy;
use App\Models\User;
use App\Services\StaffAccountService;
use Illuminate\Http\JsonResponse;

class StaffAccountController extends Controller
{
    public function __construct(
        protected StaffAccountService $service
    ) {}

    public function storePharmacyStaff(StoreStaffRequest $request, Pharmacy $pharmacy): JsonResponse
    {
        return $this->created(
            $this->service->createPharmacyStaff($pharmacy, $request->validated())
        );
    }

    public function storeLabEmployee(StoreStaffRequest $request, Laboratory $laboratory): JsonResponse
    {
        return $this->created(
            $this->service->createLabEmployee($laboratory, $request->validated())
        );
    }

    private function created(User $user): JsonResponse
    {
        return response()->json([
            'status'  => 201,
            'message' => 'Account created',
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role->value,
            ],
        ], 201);
    }
}
