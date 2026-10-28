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
        Schema::create('agricultural_operations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('farm_crop_id')
                ->constrained('farm_crops')
                ->cascadeOnDelete();

            $table->enum('operation_type', [
                'irrigation',
                'fertilization',
                'treatment',
                'pest_control',
                'plowing',
                'pruning',
                'planting',
                'harvesting',
                'other'
            ]);

            $table->dateTime('operation_date');

            $table->string('title');

            $table->text('description')->nullable();

            $table->string('quantity')->nullable();

            $table->string('unit')->nullable();

            $table->decimal('cost', 12, 2)->nullable();

            $table->string('currency')->default('SYP');

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agricultural_operations');
    }
};
