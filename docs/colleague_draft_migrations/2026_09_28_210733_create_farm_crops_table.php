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
        Schema::create('farm_crops', function (Blueprint $table) {
            $table->id();

            $table->foreignId('farm_field_id')
                ->constrained('farm_fields')
                ->cascadeOnDelete();

            $table->foreignId('crop_type_id')
                ->constrained('crop_types')
                ->restrictOnDelete();

            $table->foreignId('crop_variety_id')
                ->nullable()
                ->constrained('crop_varieties')
                ->nullOnDelete();

            $table->foreignId('season_id')
                ->nullable()
                ->constrained('agricultural_seasons')
                ->nullOnDelete();

            $table->date('planting_date')->nullable();

            $table->date('expected_harvest_date')->nullable();

            $table->date('actual_harvest_date')->nullable();

            $table->decimal('planted_area', 12, 2)->nullable();

            $table->string('area_unit')->default('dunum');

            $table->string('growth_stage')->nullable();

            $table->enum('status', [
                'planned',
                'planted',
                'growing',
                'harvested',
                'completed',
                'cancelled'
            ])->default('planned');

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farm_crops');
    }
};
