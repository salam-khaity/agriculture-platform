<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('specialist_specializations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('specialist_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('specialization_id')
                ->constrained('specializations')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(
                ['specialist_id', 'specialization_id'],
                'spec_spec_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('specialist_specializations');
    }
};
