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
        Schema::create('plant_productions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_id')->constrained()->cascadeOnDelete();
            $table->foreignId('crop_id')->constrained();
            $table->foreignId('crop_variety_id')->nullable()->constrained('crop_varieties');
            $table->foreignId('growth_stage_id')->constrained('growth_stages');
            $table->decimal('planted_area', 10, 2);
            $table->date('planting_date');
            $table->string('season')->comment('spring, summer, autumn, winter');
            $table->unsignedSmallInteger('season_year');
            $table->date('expected_harvest_date')->nullable();
            $table->date('actual_harvest_date')->nullable();
            $table->decimal('yield_quantity', 12, 2)->nullable()->comment('in kg');
            $table->string('status')->default('active')->comment('active, harvested, failed');
            $table->string('failure_reason', 500)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['field_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plant_productions');
    }
};
