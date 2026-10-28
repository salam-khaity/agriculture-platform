<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductService extends BaseCrudService
{
    protected string $model = Product::class;

    protected array $with = ['category'];

    public function create(array $data): Model
    {
        return parent::create($this->handleImage($data));
    }

    public function update(Model $model, array $data): Model
    {
        $oldImage = $model->image;
        $hasNewImage = ($data['image'] ?? null) instanceof UploadedFile;

        $product = parent::update($model, $this->handleImage($data));

        if ($hasNewImage && $oldImage) {
            Storage::disk('public')->delete($oldImage);
        }

        return $product;
    }

    // الحذف Soft Delete: الصورة تبقى لأن المنتج قد يظهر في طلبات سابقة

    protected function applyFilters(Builder $query, array $filters): void
    {
        parent::applyFilters($query, $filters);

        $query
            ->when($filters['category_id'] ?? null, fn (Builder $q, $id) => $q->where('product_category_id', $id))
            ->when(
                filter_var($filters['low_stock'] ?? false, FILTER_VALIDATE_BOOLEAN),
                fn (Builder $q) => $q->whereColumn('stock_quantity', '<=', 'min_stock_level')
            );
    }

    private function handleImage(array $data): array
    {
        if (($data['image'] ?? null) instanceof UploadedFile) {
            $data['image'] = $data['image']->store('products', 'public');
        } else {
            unset($data['image']);
        }

        return $data;
    }
}
