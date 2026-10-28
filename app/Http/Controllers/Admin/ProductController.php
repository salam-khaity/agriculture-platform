<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(private readonly ProductService $service)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $items = $this->service->paginate(
            $request->only(['search', 'category_id', 'is_active', 'low_stock']),
            (int) $request->query('per_page', 15)
        );

        return response()->json($items);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());

        return response()->json(['message' => 'تمت الإضافة بنجاح', 'data' => $item], 201);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json(['data' => $this->service->find($product)]);
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $item = $this->service->update($product, $request->validated());

        return response()->json(['message' => 'تم التحديث بنجاح', 'data' => $item]);
    }

    public function destroy(Product $product): JsonResponse
    {
        $this->service->delete($product);

        return response()->json(['message' => 'تم الحذف بنجاح']);
    }
}
