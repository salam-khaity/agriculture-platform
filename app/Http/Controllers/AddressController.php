<?php

namespace App\Http\Controllers;

use App\Http\Requests\Addresses\StoreAddressRequest;
use App\Http\Requests\Addresses\UpdateAddressRequest;
use App\Services\AddressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function __construct(
        private readonly AddressService $addressService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'search' => ['sometimes', 'nullable', 'string', 'max:255'],
            'region_id' => ['sometimes', 'integer', 'exists:regions,id'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Addresses retrieved successfully.',
            'data' => $this->addressService->index(
                $request->user(),
                $filters
            ),
        ]);
    }

    public function store(
        StoreAddressRequest $request
    ): JsonResponse {
        $address = $this->addressService->store(
            $request->user(),
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Address created successfully.',
            'data' => $address,
        ], 201);
    }

    public function show(
        Request $request,
        int $address
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => 'Address retrieved successfully.',
            'data' => $this->addressService->show(
                $request->user(),
                $address
            ),
        ]);
    }

    public function update(
        UpdateAddressRequest $request,
        int $address
    ): JsonResponse {
        $updatedAddress = $this->addressService->update(
            $request->user(),
            $address,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Address updated successfully.',
            'data' => $updatedAddress,
        ]);
    }

    public function destroy(
        Request $request,
        int $address
    ): JsonResponse {
        $this->addressService->destroy(
            $request->user(),
            $address
        );

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully.',
        ]);
    }
}
