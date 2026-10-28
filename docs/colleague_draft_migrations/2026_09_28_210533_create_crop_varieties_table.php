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
        Schema::create('crop_varieties', function (Blueprint $table) {
            $table->id();

            $table->foreignId('crop_type_id')
                ->constrained('crop_types')
                ->cascadeOnDelete();

            $table->string('name');

            $table->text('description')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique([
                'crop_type_id',
                'name'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crop_varieties');
    }
};
