<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Region\StoreRegionRequest;
use App\Http\Requests\Region\UpdateRegionRequest;
use App\Models\Region;
use App\Services\RegionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RegionController extends Controller
{
    public function __construct(
        private readonly RegionService $regionService
    ) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'Regions retrieved successfully.',
            'data' => $this->regionService->index(
                $request->only([
                    'search',
                    'is_active',
                    'per_page',
                ])
            ),
        ]);
    }

    public function store(StoreRegionRequest $request): JsonResponse
    {
        $region = $this->regionService->store(
            $request->validated()
        );

        return response()->json([
            'message' => 'Region created successfully.',
            'data' => $region,
        ], Response::HTTP_CREATED);
    }

    public function show(Region $region): JsonResponse
    {
        return response()->json([
            'message' => 'Region retrieved successfully.',
            'data' => $this->regionService->show($region),
        ]);
    }

    public function update(
        UpdateRegionRequest $request,
        Region $region
    ): JsonResponse {
        $updatedRegion = $this->regionService->update(
            $region,
            $request->validated()
        );

        return response()->json([
            'message' => 'Region updated successfully.',
            'data' => $updatedRegion,
        ]);
    }

    public function destroy(Region $region): JsonResponse
    {
        $this->regionService->destroy($region);

        return response()->json([
            'message' => 'Region deleted successfully.',
        ]);
    }
}
