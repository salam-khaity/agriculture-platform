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
        Schema::create('farms', function (Blueprint $table) {
            $table->id();

            $table->foreignId('farmer_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('name');

            $table->text('description')->nullable();

            $table->enum('activity_type', [
                'plant',
                'animal',
                'mixed'
            ]);

            $table->decimal('area', 12, 2)->nullable();

            $table->string('area_unit')->default('dunum');

            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farms');
    }
};
