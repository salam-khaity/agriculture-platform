<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        $regions = [
            'دمشق',
            'ريف دمشق',
            'حلب',
            'حمص',
            'حماة',
            'اللاذقية',
            'طرطوس',
            'إدلب',
            'درعا',
            'السويداء',
            'القنيطرة',
            'دير الزور',
            'الرقة',
            'الحسكة',
        ];

        foreach ($regions as $name) {
            Region::firstOrCreate(
                ['name' => $name],
                ['is_active' => true]
            );
        }
    }
}
