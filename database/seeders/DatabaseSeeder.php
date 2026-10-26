<?php

namespace Database\Seeders;

use App\Models\Farm;
use App\Models\Field;
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
        // 1) Catalog data first (other data depends on it)
        $this->call(CropSeeder::class);

        // 2) Fixed accounts: predictable credentials for testing
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

        // 3) Fake farms with fields for the test farmer
        Farm::factory(3)
            ->for($farmer, 'owner')
            ->has(Field::factory(2))
            ->create();

        // 4) Extra random farmers with their own farms
        User::factory(5)
            ->create(['role' => 'farmer'])
            ->each(fn (User $user) => Farm::factory(2)->create(['user_id' => $user->id]));

        // 5) Print tokens so they can be pasted into Postman
        $this->command->info('Admin token:  ' . $admin->createToken('admin')->plainTextToken);
        $this->command->info('Farmer token: ' . $farmer->createToken('farmer')->plainTextToken);
    }
}
