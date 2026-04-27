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
        Schema::create('break_rules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('shift_id')->constrained()->cascadeOnDelete();

            $table->string('name'); // Lunch, Short Break
            $table->time('start_time')->nullable(); // optional (fixed break)
            $table->time('end_time')->nullable();   // optional

            $table->integer('duration_minutes')->nullable(); // fleksibel

            $table->boolean('is_paid')->default(true);
            $table->boolean('is_flexible')->default(false); // bebas kapan saja

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('break_rules');
    }
};
