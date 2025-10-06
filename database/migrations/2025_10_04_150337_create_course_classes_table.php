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
        Schema::create('course_classes', function (Blueprint $table) {
            $table->id();
            $table->string('class_name'); // e.g., "CMA Part 1 – Oct 2025"
            $table->string('class_code')->unique(); // Internal class identifier
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('category_id'); // Department
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['registration', 'in_progress', 'completed'])->default('registration');
            $table->decimal('default_price', 10, 2)->nullable(); // Default price for this class
            $table->text('description')->nullable();
            $table->integer('max_students')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_classes');
    }
};
