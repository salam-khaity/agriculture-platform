<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // بيانات أساسية لازمة دائماً (حساب الأدمن الوحيد + القوائم + كتالوج المحاصيل)
        $this->call([
            AdminSeeder::class,
            RegionSeeder::class,
            SpecializationSeeder::class,
            ProductionTypeSeeder::class,
            LabSampleTypeSeeder::class,
            LabTestSeeder::class,
            LaboratorySeeder::class,
            CropSeeder::class,
        ]);

        // بيانات تجريبية وهمية: لا تعمل على بيئة الإنتاج
        if (! app()->environment('production')) {
            $this->call(DemoDataSeeder::class);
        }
    }
}
