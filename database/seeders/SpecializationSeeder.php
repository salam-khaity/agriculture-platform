<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpecializationSeeder extends Seeder
{
    public function run(): void
    {
        $specializations = [
            [
                'name' => 'أمراض النبات',
                'description' => 'تشخيص أمراض النباتات ومكافحتها',
            ],
            [
                'name' => 'وقاية النبات',
                'description' => 'إدارة الآفات وطرق الوقاية',
            ],
            [
                'name' => 'التربة والتسميد',
                'description' => 'تحليل التربة وإدارة التسميد',
            ],
            [
                'name' => 'الإنتاج النباتي',
                'description' => 'إدارة المحاصيل والإنتاج النباتي',
            ],
            [
                'name' => 'الري',
                'description' => 'إدارة الري واحتياجات المحاصيل المائية',
            ],
        ];

        foreach ($specializations as $specialization) {
            DB::table('specializations')->updateOrInsert(
                ['name' => $specialization['name']],
                [
                    'description' => $specialization['description'],
                    'is_active' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
