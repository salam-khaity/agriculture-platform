<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePharmacyRequest;
use App\Http\Requests\Admin\UpdatePharmacyRequest;
use App\Models\Pharmacy;
use App\Services\PharmacyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PharmacyController extends Controller
{
    public function __construct(
        private readonly PharmacyService $pharmacyService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'search' => ['sometimes', 'nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'nullable', 'in:0,1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pharmacies retrieved successfully.',
            'data' => $this->pharmacyService->index($filters),
        ]);
    }

    public function store(StorePharmacyRequest $request): JsonResponse
    {
        $pharmacy = $this->pharmacyService->store(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Pharmacy created successfully.',
            'data' => $pharmacy,
        ], 201);
    }

    public function show(Pharmacy $pharmacy): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Pharmacy retrieved successfully.',
            'data' => $this->pharmacyService->show($pharmacy),
        ]);
    }

    public function update(
        UpdatePharmacyRequest $request,
        Pharmacy $pharmacy
    ): JsonResponse {
        $updatedPharmacy = $this->pharmacyService->update(
            $pharmacy,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Pharmacy updated successfully.',
            'data' => $updatedPharmacy,
        ]);
    }

    public function destroy(Pharmacy $pharmacy): JsonResponse
    {
        $this->pharmacyService->destroy($pharmacy);

        return response()->json([
            'success' => true,
            'message' => 'Pharmacy deleted successfully.',
        ]);
    }
}
