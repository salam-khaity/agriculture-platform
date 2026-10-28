<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lab_sample_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('lab_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_sample_type_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('unit')->nullable();
            $table->string('method')->nullable();
            $table->decimal('reference_min', 12, 4)->nullable();
            $table->decimal('reference_max', 12, 4)->nullable();
            $table->string('reference_text')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_tests');
        Schema::dropIfExists('lab_sample_types');
    }
};
