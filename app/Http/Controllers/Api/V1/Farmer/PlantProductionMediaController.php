<?php

namespace App\Http\Controllers\Api\V1\Farmer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Farmer\Media\StoreMediaRequest;
use App\Http\Resources\Media\MediaResource;
use App\Models\PlantProduction;
use App\Services\Media\MediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class PlantProductionMediaController extends Controller
{
    public function __construct(
        private readonly MediaService $mediaService,
    ) {}

    public function index(PlantProduction $plantProduction): AnonymousResourceCollection
    {
        Gate::authorize('view', $plantProduction);

        return MediaResource::collection($plantProduction->media);
    }

    public function store(StoreMediaRequest $request, PlantProduction $plantProduction): JsonResponse
    {
        Gate::authorize('update', $plantProduction);

        $media = $this->mediaService->attach($plantProduction, $request->file('files'), $request->user());

        return $this->resource(
            MediaResource::collection($media),
            'Media uploaded successfully.',
            Response::HTTP_CREATED
        );
    }
}
