<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLaboratoryRequest;
use App\Http\Requests\Admin\UpdateLaboratoryRequest;
use App\Models\Laboratory;
use App\Services\LaboratoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LaboratoryController extends Controller
{
    public function __construct(
        private readonly LaboratoryService $laboratoryService
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
            'message' => 'Laboratories retrieved successfully.',
            'data' => $this->laboratoryService->index($filters),
        ]);
    }

    public function store(StoreLaboratoryRequest $request): JsonResponse
    {
        $laboratory = $this->laboratoryService->store(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Laboratory created successfully.',
            'data' => $laboratory,
        ], 201);
    }

    public function show(Laboratory $laboratory): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Laboratory retrieved successfully.',
            'data' => $this->laboratoryService->show($laboratory),
        ]);
    }

    public function update(
        UpdateLaboratoryRequest $request,
        Laboratory $laboratory
    ): JsonResponse {
        $updatedLaboratory = $this->laboratoryService->update(
            $laboratory,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Laboratory updated successfully.',
            'data' => $updatedLaboratory,
        ]);
    }

    public function destroy(Laboratory $laboratory): JsonResponse
    {
        $this->laboratoryService->destroy($laboratory);

        return response()->json([
            'success' => true,
            'message' => 'Laboratory deleted successfully.',
        ]);
    }
}
