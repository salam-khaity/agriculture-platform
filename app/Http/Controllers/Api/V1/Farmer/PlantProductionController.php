<?php

namespace App\Http\Controllers\Api\V1\Farmer;

use App\Enums\ProductionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Farmer\PlantProduction\ChangeStageRequest;
use App\Http\Requests\Farmer\PlantProduction\FailPlantProductionRequest;
use App\Http\Requests\Farmer\PlantProduction\HarvestPlantProductionRequest;
use App\Http\Requests\Farmer\PlantProduction\StorePlantProductionRequest;
use App\Http\Requests\Farmer\PlantProduction\UpdatePlantProductionRequest;
use App\Http\Resources\Farmer\PlantProductionResource;
use App\Models\Field;
use App\Models\PlantProduction;
use App\Services\PlantProduction\PlantProductionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class PlantProductionController extends Controller
{
    public function __construct(
        private readonly PlantProductionService $productionService,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Field $field): AnonymousResourceCollection
    {
        Gate::authorize('view', $field);

        $validated = $request->validate([
            'status' => ['nullable', Rule::enum(ProductionStatus::class)],
        ]);

        return PlantProductionResource::collection(
            $this->productionService->listForField($field, $validated['status'] ?? null)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePlantProductionRequest $request, Field $field): JsonResponse
    {
        Gate::authorize('update', $field);

        $production = $this->productionService->create($field, $request->user(), $request->validated());

        return $this->resource(
            new PlantProductionResource($production),
            'Plant production created successfully.',
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(PlantProduction $plantProduction): PlantProductionResource
    {
        Gate::authorize('view', $plantProduction);

        return new PlantProductionResource($this->productionService->loadDetails($plantProduction));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePlantProductionRequest $request, PlantProduction $plantProduction): JsonResponse
    {
        Gate::authorize('update', $plantProduction);

        $production = $this->productionService->update($plantProduction, $request->validated());

        return $this->resource(new PlantProductionResource($production), 'Plant production updated successfully.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PlantProduction $plantProduction): JsonResponse
    {
        Gate::authorize('delete', $plantProduction);

        $this->productionService->delete($plantProduction);

        return $this->message('Plant production deleted successfully.');
    }

    public function changeStage(ChangeStageRequest $request, PlantProduction $plantProduction): JsonResponse
    {
        Gate::authorize('update', $plantProduction);

        $production = $this->productionService->changeStage(
            $plantProduction,
            (int) $request->validated('growth_stage_id'),
            $request->user()
        );

        return $this->resource(new PlantProductionResource($production), 'Growth stage changed successfully.');
    }

    public function harvest(HarvestPlantProductionRequest $request, PlantProduction $plantProduction): JsonResponse
    {
        Gate::authorize('update', $plantProduction);

        $production = $this->productionService->harvest($plantProduction, $request->validated());

        return $this->resource(new PlantProductionResource($production), 'Plant production harvested successfully.');
    }

    public function fail(FailPlantProductionRequest $request, PlantProduction $plantProduction): JsonResponse
    {
        Gate::authorize('update', $plantProduction);

        $production = $this->productionService->fail($plantProduction, $request->validated('failure_reason'));

        return $this->resource(new PlantProductionResource($production), 'Plant production marked as failed.');
    }


}
