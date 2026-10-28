<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('specialist_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('specialization_id')->constrained('specializations')->restrictOnDelete();
            $table->text('bio')->nullable();
            $table->unsignedInteger('experience_years')->nullable();

            $table->timestamps();
        });
        Schema::create('specialist_production_type', function (Blueprint $table) {
            $table->foreignId('specialist_profile_id')->constrained('specialist_profiles')->cascadeOnDelete();
            $table->foreignId('production_type_id')->constrained('production_types')->cascadeOnDelete();
            $table->primary(['specialist_profile_id', 'production_type_id']);
        });

        Schema::create('specialist_region', function (Blueprint $table) {
            $table->foreignId('specialist_profile_id')->constrained('specialist_profiles')->cascadeOnDelete();
            $table->foreignId('region_id')->constrained('regions')->cascadeOnDelete();
            $table->primary(['specialist_profile_id', 'region_id']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('specialist_profiles');
    }
};
