<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLabSampleTypeRequest;
use App\Http\Requests\Admin\UpdateLabSampleTypeRequest;
use App\Models\LabSampleType;
use App\Services\LabSampleTypeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LabSampleTypeController extends Controller
{
    public function __construct(private readonly LabSampleTypeService $service)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $items = $this->service->paginate(
            $request->only(['search', 'is_active']),
            (int) $request->query('per_page', 15)
        );

        return response()->json($items);
    }

    public function store(StoreLabSampleTypeRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());

        return response()->json(['message' => 'تمت الإضافة بنجاح', 'data' => $item], 201);
    }

    public function show(LabSampleType $sample_type): JsonResponse
    {
        return response()->json(['data' => $this->service->find($sample_type)]);
    }

    public function update(UpdateLabSampleTypeRequest $request, LabSampleType $sample_type): JsonResponse
    {
        $item = $this->service->update($sample_type, $request->validated());

        return response()->json(['message' => 'تم التحديث بنجاح', 'data' => $item]);
    }

    public function destroy(LabSampleType $sample_type): JsonResponse
    {
        $this->service->delete($sample_type);

        return response()->json(['message' => 'تم الحذف بنجاح']);
    }
}
