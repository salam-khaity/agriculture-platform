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
        Schema::create('plant_production_stage_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plant_production_id')->constrained()->cascadeOnDelete();
            $table->foreignId('growth_stage_id')->constrained('growth_stages');
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('changed_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plant_production_stage_histories');
    }
};
