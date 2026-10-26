<?php

namespace App\Http\Controllers\Api\V1\Farmer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Crop\CropResource;
use App\Models\Crop;
use App\Services\Crop\CropService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CropCatalogController extends Controller
{
    public function __construct(
        private readonly CropService $cropService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return CropResource::collection($this->cropService->listActiveWithDetails());
    }

    public function show(Crop $crop): CropResource
    {
        return new CropResource($this->cropService->showForFarmer($crop));
    }
}
