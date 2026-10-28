<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LaboratorySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $laboratories = [
            [
                'name' => 'مختبر غراس الزراعي التجريبي',
                'manager_name' => 'مدير المختبر التجريبي',
                'email' => 'ghars.lab@example.com',
                'description' => 'مختبر تجريبي لتحليل عينات التربة والمياه والأنسجة النباتية.',
                'phone' => '0991000001',
            ],
            [
                'name' => 'مختبر تحليل التربة والمياه',
                'manager_name' => 'مسؤول التحاليل',
                'email' => 'soil.water.lab@example.com',
                'description' => 'مختبر تجريبي متخصص في تحاليل التربة ومياه الري.',
                'phone' => '0991000002',
            ],
        ];

        foreach ($laboratories as $laboratory) {
            DB::table('laboratories')->updateOrInsert(
                ['email' => $laboratory['email']],
                [
                    'name' => $laboratory['name'],
                    'address_id' => null,
                    'manager_name' => $laboratory['manager_name'],
                    'description' => $laboratory['description'],
                    'phone' => $laboratory['phone'],
                    'is_active' => true,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }
}
