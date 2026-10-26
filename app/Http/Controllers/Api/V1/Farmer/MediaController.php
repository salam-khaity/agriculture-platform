<?php

namespace App\Http\Controllers\Api\V1\Farmer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Farmer\Media\ReplaceMediaRequest;
use App\Http\Resources\Media\MediaResource;
use App\Models\Media;
use App\Services\Media\MediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MediaController extends Controller
{
    public function __construct(
        private readonly MediaService $mediaService,
    ) {}

    public function replace(ReplaceMediaRequest $request, Media $media): JsonResponse
    {
        Gate::authorize('update', $media);

        $media = $this->mediaService->replace($media, $request->file('file'));

        return $this->resource(new MediaResource($media), 'Media replaced successfully.');
    }

    public function destroy(Media $media): JsonResponse
    {
        Gate::authorize('delete', $media);

        $this->mediaService->delete($media);

        return $this->message('Media deleted successfully.');
    }

}
