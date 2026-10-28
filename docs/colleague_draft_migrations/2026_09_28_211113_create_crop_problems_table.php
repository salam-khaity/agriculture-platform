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
        Schema::create('crop_problems', function (Blueprint $table) {
            $table->id();

            $table->foreignId('farm_crop_id')
                ->constrained('farm_crops')
                ->cascadeOnDelete();

            $table->string('title');

            $table->text('description');

            $table->string('growth_stage')->nullable();

            $table->enum('severity', [
                'low',
                'medium',
                'high',
                'critical'
            ])->default('medium');

            $table->enum('status', [
                'open',
                'under_review',
                'diagnosed',
                'resolved',
                'closed'
            ])->default('open');

            $table->timestamp('reported_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crop_problems');
    }
};
