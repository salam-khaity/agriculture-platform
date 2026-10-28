<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLabTestRequest;
use App\Http\Requests\Admin\UpdateLabTestRequest;
use App\Models\LabTest;
use App\Services\LabTestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LabTestController extends Controller
{
    public function __construct(private readonly LabTestService $service)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $items = $this->service->paginate(
            $request->only(['search', 'lab_sample_type_id', 'is_active']),
            (int) $request->query('per_page', 15)
        );

        return response()->json($items);
    }

    public function store(StoreLabTestRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());

        return response()->json(['message' => 'تمت الإضافة بنجاح', 'data' => $item], 201);
    }

    public function show(LabTest $lab_test): JsonResponse
    {
        return response()->json(['data' => $this->service->find($lab_test)]);
    }

    public function update(UpdateLabTestRequest $request, LabTest $lab_test): JsonResponse
    {
        $item = $this->service->update($lab_test, $request->validated());

        return response()->json(['message' => 'تم التحديث بنجاح', 'data' => $item]);
    }

    public function destroy(LabTest $lab_test): JsonResponse
    {
        $this->service->delete($lab_test);

        return response()->json(['message' => 'تم الحذف بنجاح']);
    }
}
