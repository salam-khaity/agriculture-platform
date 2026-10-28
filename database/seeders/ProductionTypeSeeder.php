<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductionTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'الإنتاج النباتي',
            'إنتاج الأشجار المثمرة',
            'إنتاج الخضروات',
            'إنتاج المحاصيل الحقلية',
            'إنتاج البيوت المحمية',
        ];

        foreach ($types as $name) {
            DB::table('production_types')->updateOrInsert(
                ['name' => $name],
                [
                    'is_active' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
