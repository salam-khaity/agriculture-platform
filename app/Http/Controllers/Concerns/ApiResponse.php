<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

trait ApiResponse
{
    protected function message(string $message, int $status = 200): JsonResponse
    {
        return response()->json(['message' => $message], $status);
    }

    protected function resource(JsonResource|ResourceCollection $resource, string $message, int $status = 200): JsonResponse
    {
        return $resource
            ->additional(['message' => $message])
            ->response()
            ->setStatusCode($status);
    }
}
