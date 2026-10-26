<?php

namespace App\Http\Controllers\Api\V1\Farmer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Farmer\Media\StoreMediaRequest;
use App\Http\Resources\Media\MediaResource;
use App\Models\Field;
use App\Services\Media\MediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class FieldMediaController extends Controller
{
    public function __construct(
        private readonly MediaService $mediaService,
    ) {}

    public function index(Field $field): AnonymousResourceCollection
    {
        Gate::authorize('view', $field);

        return MediaResource::collection($field->media);
    }

    public function store(StoreMediaRequest $request, Field $field): JsonResponse
    {
        Gate::authorize('update', $field);

        $media = $this->mediaService->attach($field, $request->file('files'), $request->user());

        return $this->resource(
            MediaResource::collection($media),
            'Media uploaded successfully.',
            Response::HTTP_CREATED
        );
    }

}
