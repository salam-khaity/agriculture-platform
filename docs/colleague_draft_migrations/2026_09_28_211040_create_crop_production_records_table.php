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
        Schema::create('crop_production_records', function (Blueprint $table) {
            $table->id();

            $table->foreignId('farm_crop_id')
                ->constrained('farm_crops')
                ->cascadeOnDelete();

            $table->date('production_date');

            $table->decimal('quantity', 12, 2);

            $table->string('unit')->default('kg');

            $table->decimal('selling_price', 12, 2)->nullable();

            $table->decimal('total_revenue', 12, 2)->nullable();

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
        Schema::dropIfExists('crop_production_records');
    }
};
