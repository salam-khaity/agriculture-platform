<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Crop;
use App\Models\Farm;
use App\Models\Field;
use App\Models\PlantProduction;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $farmer = User::factory()->create([
            'name'   => 'Test Farmer',
            'email'  => 'farmer@agri.test',
            'role'   => UserRole::Farmer,
            'status' => UserStatus::Active,
        ]);

        // المزارع أولاً: الإنتاج النباتي أدناه يحتاج حقلاً موجوداً
        Farm::factory(3)
            ->for($farmer, 'owner')
            ->has(Field::factory(2))
            ->create();

        $tomato = Crop::where('name', 'Tomato')->first();
        $field  = $farmer->farms()->first()->fields()->first();
        $stage  = $tomato->growthStages()->ordered()->first();

        $production = PlantProduction::create([
            'field_id'        => $field->id,
            'crop_id'         => $tomato->id,
            'crop_variety_id' => $tomato->varieties()->first()->id,
            'growth_stage_id' => $stage->id,
            'planted_area'    => $field->area,
            'planting_date'   => now()->subDays(30)->toDateString(),
            'season'          => 'spring',
            'season_year'     => (int) now()->year,
            'status'          => 'active',
        ]);

        $production->stageHistory()->create([
            'growth_stage_id' => $stage->id,
            'changed_by'      => $farmer->id,
            'changed_at'      => now()->subDays(30),
        ]);

        User::factory(5)
            ->create([
                'role'   => UserRole::Farmer,
                'status' => UserStatus::Active,
            ])
            ->each(fn (User $user) => Farm::factory(2)->create(['user_id' => $user->id]));

        // توكنات للاستخدام في Postman (admin_token و token)
        $admin = User::where('role', UserRole::Admin)->first();

        if ($admin) {
            $this->command->info('Admin token:  ' . $admin->createToken('admin')->plainTextToken);
        }

        $this->command->info('Farmer token: ' . $farmer->createToken('farmer')->plainTextToken);
    }
}
