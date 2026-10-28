<?php

namespace Database\Seeders;

use App\Models\Crop;
use App\Models\Farm;
use App\Models\Field;
use App\Models\PlantProduction;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(CropSeeder::class);

        $admin = User::factory()->create([
            'name'  => 'System Admin',
            'email' => 'admin@agri.test',
            'role'  => 'admin',
        ]);

        $farmer = User::factory()->create([
            'name'  => 'Test Farmer',
            'email' => 'farmer@agri.test',
            'role'  => 'farmer',
        ]);

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

        Farm::factory(3)
            ->for($farmer, 'owner')
            ->has(Field::factory(2))
            ->create();

        User::factory(5)
            ->create(['role' => 'farmer'])
            ->each(fn (User $user) => Farm::factory(2)->create(['user_id' => $user->id]));

        $this->command->info('Admin token:  ' . $admin->createToken('admin')->plainTextToken);
        $this->command->info('Farmer token: ' . $farmer->createToken('farmer')->plainTextToken);
    }
}
