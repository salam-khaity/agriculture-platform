<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GrowthStage\StoreGrowthStageRequest;
use App\Http\Requests\Admin\GrowthStage\UpdateGrowthStageRequest;
use App\Http\Resources\Crop\GrowthStageResource;
use App\Models\Crop;
use App\Models\GrowthStage;
use App\Services\Crop\GrowthStageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class GrowthStageController extends Controller
{

    public function __construct(
        private readonly GrowthStageService $stageService,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Crop $crop): AnonymousResourceCollection
    {
        return GrowthStageResource::collection($this->stageService->listForCrop($crop));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGrowthStageRequest $request, Crop $crop): JsonResponse
    {
        $stage = $this->stageService->create($crop, $request->validated());

        return $this->resource(new GrowthStageResource($stage), 'Growth stage created successfully.', Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(GrowthStage $growthStage): GrowthStageResource
    {
        return new GrowthStageResource($growthStage);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGrowthStageRequest $request, GrowthStage $growthStage): JsonResponse
    {
        $stage = $this->stageService->update($growthStage, $request->validated());

        return $this->resource(new GrowthStageResource($stage), 'Growth stage updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GrowthStage $growthStage): JsonResponse
    {
        $this->stageService->delete($growthStage);

        return $this->message('Growth stage deleted successfully.');
    }
}
