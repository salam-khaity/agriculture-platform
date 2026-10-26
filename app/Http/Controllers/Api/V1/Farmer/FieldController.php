<?php

namespace App\Http\Controllers\Api\V1\Farmer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Farmer\Field\StoreFieldRequest;
use App\Http\Requests\Farmer\Field\UpdateFieldRequest;
use App\Http\Resources\Farmer\FieldResource;
use App\Models\Farm;
use App\Models\Field;
use App\Services\Field\FieldService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class FieldController extends Controller
{

    public function __construct(
        private readonly FieldService $fieldService,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Farm $farm): AnonymousResourceCollection
    {
        Gate::authorize('view', $farm);

        return FieldResource::collection($this->fieldService->listForFarm($farm));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFieldRequest $request, Farm $farm): JsonResponse
    {
        Gate::authorize('update', $farm);

        $field = $this->fieldService->create($farm, $request->validated());

        return $this->resource(
            new FieldResource($field),
            'Field created successfully.',
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Field $field): FieldResource
    {
        Gate::authorize('view', $field);

        $field->load('media');

        return new FieldResource($field);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFieldRequest $request, Field $field): JsonResponse
    {
        Gate::authorize('update', $field);

        $field = $this->fieldService->update($field, $request->validated());

        return $this->resource(new FieldResource($field), 'Field updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Field $field): JsonResponse
    {
        Gate::authorize('delete', $field);

        $this->fieldService->delete($field);

        return $this->message('Field deleted successfully.');
    }
}
