<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Crop\StoreCropRequest;
use App\Http\Requests\Admin\Crop\UpdateCropRequest;
use App\Http\Resources\Crop\CropResource;
use App\Models\Crop;
use App\Services\Crop\CropService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class CropController extends Controller
{

    public function __construct(
        private readonly CropService $cropService,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $crops = $this->cropService->list(
            $request->query('search'),
            $request->integer('per_page') ?: null,
        );

        return CropResource::collection($crops);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCropRequest $request): JsonResponse
    {
        $crop = $this->cropService->create($request->validated());

        return $this->resource(new CropResource($crop), 'Crop created successfully.', Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Crop $crop): CropResource
    {
        return new CropResource($this->cropService->loadDetails($crop));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCropRequest $request, Crop $crop): JsonResponse
    {
        $crop = $this->cropService->update($crop, $request->validated());

        return $this->resource(new CropResource($crop), 'Crop updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Crop $crop): JsonResponse
    {
        $this->cropService->delete($crop);

        return $this->message('Crop deleted successfully.');
    }
}
