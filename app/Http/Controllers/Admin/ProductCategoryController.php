<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductCategoryRequest;
use App\Http\Requests\Admin\UpdateProductCategoryRequest;
use App\Models\ProductCategory;
use App\Services\ProductCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function __construct(private readonly ProductCategoryService $service)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $items = $this->service->paginate(
            $request->only(['search', 'type', 'is_active']),
            (int) $request->query('per_page', 15)
        );

        return response()->json($items);
    }

    public function store(StoreProductCategoryRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());

        return response()->json(['message' => 'تمت الإضافة بنجاح', 'data' => $item], 201);
    }

    public function show(ProductCategory $product_category): JsonResponse
    {
        return response()->json(['data' => $this->service->find($product_category)]);
    }

    public function update(UpdateProductCategoryRequest $request, ProductCategory $product_category): JsonResponse
    {
        $item = $this->service->update($product_category, $request->validated());

        return response()->json(['message' => 'تم التحديث بنجاح', 'data' => $item]);
    }

    public function destroy(ProductCategory $product_category): JsonResponse
    {
        $this->service->delete($product_category);

        return response()->json(['message' => 'تم الحذف بنجاح']);
    }
}
