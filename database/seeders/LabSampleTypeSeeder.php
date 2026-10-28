<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LabSampleTypeSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $sampleTypes = [
            [
                'name' => 'عينة تربة',
                'description' => 'تحليل خصائص التربة والعناصر الغذائية ودرجة الحموضة.',
            ],
            [
                'name' => 'عينة مياه ري',
                'description' => 'تحليل جودة مياه الري والملوحة ودرجة الحموضة.',
            ],
            [
                'name' => 'عينة نبات',
                'description' => 'فحص الأنسجة النباتية للمساعدة في تحديد الأمراض أو نقص العناصر.',
            ],
            [
                'name' => 'عينة أوراق',
                'description' => 'تحليل الأوراق لتقييم الحالة الغذائية للنبات.',
            ],
            [
                'name' => 'عينة جذور',
                'description' => 'فحص الجذور للكشف عن المشكلات المحتملة.',
            ],
        ];

        foreach ($sampleTypes as $sampleType) {
            DB::table('lab_sample_types')->updateOrInsert(
                ['name' => $sampleType['name']],
                [
                    'description' => $sampleType['description'],
                    'is_active' => true,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }
}
