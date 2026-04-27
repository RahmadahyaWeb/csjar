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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');

            $table->enum('status', ['present', 'absent', 'leave', 'holiday'])->default('present');

            $table->timestamp('checkin_at')->nullable();
            $table->timestamp('checkout_at')->nullable();

            $table->integer('work_minutes')->default(0);
            $table->integer('break_minutes')->default(0);

            $table->integer('late_minutes')->default(0);
            $table->integer('early_leave_minutes')->default(0);
            $table->integer('overtime_minutes')->default(0);

            $table->boolean('is_overtime_approved')->default(false);

            $table->string('overtime_status')->default('none'); // none|pending|approved|rejected
            $table->foreignId('overtime_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('overtime_approved_at')->nullable();

            $table->boolean('is_locked')->default(false)->index();

            $table->timestamps();

            $table->unique(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
