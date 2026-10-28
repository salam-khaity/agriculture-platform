<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('specialist_availabilities', function (Blueprint $table) {
            $table->id();

            $table->foreignId('specialist_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('day_of_week');

            $table->time('start_time');
            $table->time('end_time');

            $table->boolean('is_available')->default(true);

            $table->timestamps();

            $table->unique(
                ['specialist_id', 'day_of_week', 'start_time', 'end_time'],
                'spec_avail_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('specialist_availabilities');
    }
};
