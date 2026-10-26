<?php

namespace App\Services\Crop;

use App\Models\Crop;
use App\Models\GrowthStage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class GrowthStageService
{
    public function listForCrop(Crop $crop): Collection
    {
        return $crop->growthStages()->ordered()->get();
    }

    public function create(Crop $crop, array $data): GrowthStage
    {

        return DB::transaction(function () use ($crop, $data) {

            Crop::query()->lockForUpdate()->findOrFail($crop->id);

            $last = (int) $crop->growthStages()->max('sort_order');

            $position = min($data['sort_order'] ?? ($last + 1), $last + 1);

            $crop->growthStages()
                ->where('sort_order', '>=', $position)
                ->increment('sort_order');

            $data['sort_order'] = $position;

            return $crop->growthStages()->create($data);
        });
    }

    public function update(GrowthStage $stage, array $data): GrowthStage
    {
        return DB::transaction(function () use ($stage, $data) {
            if (isset($data['sort_order'])) {
                Crop::query()->lockForUpdate()->findOrFail($stage->crop_id);

                $this->move($stage, (int) $data['sort_order']);
            }

            unset($data['sort_order']);

            if ($data !== []) {
                $stage->update($data);
            }

            return $stage->refresh();
        });
    }

    public function delete(GrowthStage $stage): void
    {
        // TODO (plant_productions step): block deleting a stage that is currently in use
        DB::transaction(function () use ($stage) {
            Crop::query()->lockForUpdate()->findOrFail($stage->crop_id);

            $stage->delete();

            GrowthStage::query()
                ->where('crop_id', $stage->crop_id)
                ->where('sort_order', '>', $stage->sort_order)
                ->decrement('sort_order');
        });
    }

    private function move(GrowthStage $stage, int $target): void
    {
        $last = (int) GrowthStage::query()->where('crop_id', $stage->crop_id)->max('sort_order');
        $target = min($target, $last);
        $current = $stage->sort_order;

        if ($target === $current) {
            return;
        }

        $others = GrowthStage::query()
            ->where('crop_id', $stage->crop_id)
            ->whereKeyNot($stage->id);

        if ($target < $current) {

            $others->whereBetween('sort_order', [$target, $current - 1])->increment('sort_order');
        } else {

            $others->whereBetween('sort_order', [$current + 1, $target])->decrement('sort_order');
        }

        $stage->update(['sort_order' => $target]);
    }

}
