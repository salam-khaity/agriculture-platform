<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CropVariety\StoreCropVarietyRequest;
use App\Http\Requests\Admin\CropVariety\UpdateCropVarietyRequest;
use App\Http\Resources\Crop\CropVarietyResource;
use App\Models\Crop;
use App\Models\CropVariety;
use App\Services\Crop\CropVarietyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class CropVarietyController extends Controller
{
    public function __construct(
        private readonly CropVarietyService $varietyService,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Crop $crop): AnonymousResourceCollection
    {
        return CropVarietyResource::collection($this->varietyService->listForCrop($crop));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCropVarietyRequest $request, Crop $crop): JsonResponse
    {
        $variety = $this->varietyService->create($crop, $request->validated());

        return $this->resource(new CropVarietyResource($variety), 'Crop variety created successfully.', Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(CropVariety $variety): CropVarietyResource
    {
        return new CropVarietyResource($variety);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCropVarietyRequest $request, CropVariety $variety): JsonResponse
    {
        $variety = $this->varietyService->update($variety, $request->validated());

        return $this->resource(new CropVarietyResource($variety), 'Crop variety updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CropVariety $variety): JsonResponse
    {
        $this->varietyService->delete($variety);

        return $this->message('Crop variety deleted successfully.');
    }
}
