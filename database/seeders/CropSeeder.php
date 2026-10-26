<?php

namespace Database\Seeders;

use App\Models\Crop;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CropSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $catalog = [
            [
                'name' => 'Tomato', 'scientific_name' => 'Solanum lycopersicum', 'category' => 'vegetable',
                'varieties' => ['Cherry', 'Beef', 'Roma', 'Local'],
                'stages'    => ['Germination', 'Seedling', 'Vegetative Growth', 'Flowering', 'Fruit Set', 'Fruit Ripening', 'Harvest'],
            ],
            [
                'name' => 'Cucumber', 'scientific_name' => 'Cucumis sativus', 'category' => 'vegetable',
                'varieties' => ['Beit Alpha', 'Long Green', 'Local'],
                'stages'    => ['Germination', 'Seedling', 'Vegetative Growth', 'Flowering', 'Fruit Development', 'Harvest'],
            ],
            [
                'name' => 'Potato', 'scientific_name' => 'Solanum tuberosum', 'category' => 'vegetable',
                'varieties' => ['Spunta', 'Diamant', 'Local'],
                'stages'    => ['Sprouting', 'Vegetative Growth', 'Tuber Initiation', 'Tuber Bulking', 'Maturity', 'Harvest'],
            ],
            [
                'name' => 'Wheat', 'scientific_name' => 'Triticum aestivum', 'category' => 'cereal',
                'varieties' => ['Cham 6', 'Cham 8', 'Durum'],
                'stages'    => ['Germination', 'Tillering', 'Stem Elongation', 'Heading', 'Grain Filling', 'Maturity'],
            ],
            [
                'name' => 'Olive', 'scientific_name' => 'Olea europaea', 'category' => 'tree',
                'varieties' => ['Zaity', 'Sorani', 'Khodeiri'],
                'stages'    => ['Dormancy', 'Bud Break', 'Flowering', 'Fruit Set', 'Fruit Growth', 'Ripening', 'Harvest'],
            ],
        ];

        foreach ($catalog as $item) {
            // updateOrCreate makes the seeder safe to run many times
            $crop = Crop::updateOrCreate(
                ['name' => $item['name']],
                [
                    'scientific_name' => $item['scientific_name'],
                    'category'        => $item['category'],
                    'is_active'       => true,
                ],
            );

            foreach ($item['varieties'] as $variety) {
                $crop->varieties()->firstOrCreate(['name' => $variety]);
            }

            foreach ($item['stages'] as $index => $stage) {
                $crop->growthStages()->updateOrCreate(
                    ['name' => $stage],
                    ['sort_order' => $index + 1],
                );
            }
        }
    }
}
