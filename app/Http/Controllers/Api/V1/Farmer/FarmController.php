<?php

namespace App\Http\Controllers\Api\V1\Farmer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Farmer\Farm\StoreFarmRequest;
use App\Http\Requests\Farmer\Farm\UpdateFarmRequest;
use App\Http\Resources\Farmer\FarmResource;
use App\Models\Farm;
use App\Services\Farm\FarmService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class FarmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(
        private readonly FarmService $farmService,
    ) {}
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = $request->integer('per_page') ?: null;

        return FarmResource::collection(
            $this->farmService->listForUser($request->user(), $perPage)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFarmRequest $request): JsonResponse
    {
        $farm = $this->farmService->create($request->user(), $request->validated());

        return $this->resource(
            new FarmResource($farm),
            'Farm created successfully.',
              Response::HTTP_CREATED
          );
    }

    /**
     * Display the specified resource.
     */
    public function show(Farm $farm): FarmResource
    {
        Gate::authorize('view', $farm);

        return new FarmResource($farm);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFarmRequest $request, Farm $farm): JsonResponse
    {
        Gate::authorize('update', $farm);

        $farm = $this->farmService->update($farm, $request->validated());

        return $this->resource(new FarmResource($farm), 'Farm updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Farm $farm): JsonResponse
    {
        Gate::authorize('delete', $farm);

        $this->farmService->delete($farm);

        return $this->message('Farm deleted successfully.');    }
}
