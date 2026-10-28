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
        Schema::create('crop_problem_attachments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('crop_problem_id')
                ->constrained('crop_problems')
                ->cascadeOnDelete();

            $table->string('file_path');

            $table->string('file_name')->nullable();

            $table->string('file_type')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crop_problem_attachments');
    }
};
