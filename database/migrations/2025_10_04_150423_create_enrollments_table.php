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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('course_class_id');
            $table->unsignedBigInteger('registered_by'); // User who registered the student
            $table->date('enrollment_date');
            $table->decimal('total_amount', 10, 2); // Total amount required for this enrollment
            $table->decimal('paid_amount', 10, 2)->default(0); // Total paid so far
            $table->decimal('due_amount', 10, 2)->default(0); // Remaining amount
            $table->enum('payment_status', ['not_paid', 'partial', 'completed'])->default('not_paid');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('course_class_id')->references('id')->on('course_classes')->onDelete('cascade');
            $table->foreign('registered_by')->references('id')->on('users')->onDelete('cascade');
            
            // Ensure a student can only be enrolled once per class
            $table->unique(['student_id', 'course_class_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
