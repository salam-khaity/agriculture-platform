<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LabTestSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $tests = [
            [
                'sample_type' => 'عينة تربة',
                'name' => 'درجة حموضة التربة',
                'unit' => 'pH',
                'method' => 'pH Meter',
                'reference_min' => null,
                'reference_max' => null,
                'reference_text' => 'تختلف القيمة المناسبة حسب المحصول ونوع التربة.',
                'price' => 50000,
            ],
            [
                'sample_type' => 'عينة تربة',
                'name' => 'الناقلية الكهربائية للتربة',
                'unit' => 'dS/m',
                'method' => 'EC Meter',
                'reference_min' => null,
                'reference_max' => null,
                'reference_text' => 'تُفسر النتيجة وفق المحصول وطريقة أخذ العينة.',
                'price' => 60000,
            ],
            [
                'sample_type' => 'عينة تربة',
                'name' => 'نسبة المادة العضوية',
                'unit' => '%',
                'method' => 'Laboratory Analysis',
                'reference_min' => null,
                'reference_max' => null,
                'reference_text' => 'تعتمد الحدود المناسبة على خصائص التربة.',
                'price' => 75000,
            ],
            [
                'sample_type' => 'عينة تربة',
                'name' => 'النيتروجين المتاح',
                'unit' => 'mg/kg',
                'method' => 'Laboratory Analysis',
                'reference_min' => null,
                'reference_max' => null,
                'reference_text' => 'تُحدد الحاجة للتسميد وفق المحصول ومرحلة نموه.',
                'price' => 80000,
            ],
            [
                'sample_type' => 'عينة مياه ري',
                'name' => 'درجة حموضة مياه الري',
                'unit' => 'pH',
                'method' => 'pH Meter',
                'reference_min' => null,
                'reference_max' => null,
                'reference_text' => 'تُفسر النتيجة وفق نوع المحصول وجودة المياه المطلوبة.',
                'price' => 40000,
            ],
            [
                'sample_type' => 'عينة مياه ري',
                'name' => 'ملوحة مياه الري',
                'unit' => 'dS/m',
                'method' => 'EC Meter',
                'reference_min' => null,
                'reference_max' => null,
                'reference_text' => 'تعتمد ملاءمة المياه على المحصول وظروف التربة.',
                'price' => 50000,
            ],
            [
                'sample_type' => 'عينة أوراق',
                'name' => 'تحليل العناصر الغذائية في الأوراق',
                'unit' => null,
                'method' => 'Plant Tissue Analysis',
                'reference_min' => null,
                'reference_max' => null,
                'reference_text' => 'تُفسر النتائج حسب نوع النبات ومرحلة نموه.',
                'price' => 100000,
            ],
            [
                'sample_type' => 'عينة نبات',
                'name' => 'الفحص المخبري للأمراض النباتية',
                'unit' => null,
                'method' => 'Laboratory Diagnosis',
                'reference_min' => null,
                'reference_max' => null,
                'reference_text' => 'تتحدد النتيجة وفق الفحص والعامل الممرض المحتمل.',
                'price' => 120000,
            ],
        ];

        $sampleTypes = DB::table('lab_sample_types')
            ->pluck('id', 'name');

        foreach ($tests as $test) {
            $sampleTypeId = $sampleTypes->get($test['sample_type']);

            if (!$sampleTypeId) {
                continue;
            }

            DB::table('lab_tests')->updateOrInsert(
                [
                    'lab_sample_type_id' => $sampleTypeId,
                    'name' => $test['name'],
                ],
                [
                    'unit' => $test['unit'],
                    'method' => $test['method'],
                    'reference_min' => $test['reference_min'],
                    'reference_max' => $test['reference_max'],
                    'reference_text' => $test['reference_text'],
                    'price' => $test['price'],
                    'is_active' => true,
                    'deleted_at' => null,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }
}
