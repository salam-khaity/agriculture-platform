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
        Schema::create('delivery_agent_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('vehicle_type')->nullable();
            $table->string('vehicle_number')->nullable();

            $table->timestamps();
        });
        Schema::create('delivery_agent_region', function (Blueprint $table) {
            $table->foreignId('delivery_agent_profile_id')->constrained('delivery_agent_profiles')->cascadeOnDelete();
            $table->foreignId('region_id')->constrained('regions')->cascadeOnDelete();
            $table->primary(['delivery_agent_profile_id', 'region_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_agent_profiles');
    }
};
