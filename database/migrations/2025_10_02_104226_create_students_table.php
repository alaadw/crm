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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_id')->unique(); // Based on primary phone number
            $table->string('full_name');
            $table->string('email')->nullable();
            $table->string('phone_primary'); // E.164 format
            $table->string('phone_alt')->nullable();
            $table->string('country_code', 5); // Country code (e.g., +962)
            $table->enum('reach_source', [
                'Social Media',
                'University Circular', 
                'Purchased Data',
                'Referral',
                'Old Student',
                'Other'
            ]);
            $table->enum('department', ['Management', 'IT', 'Engineering', 'English']);
            $table->foreignId('preferred_course_id')->nullable()->constrained('courses')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            
            // Indexes for better performance
            $table->index('phone_primary');
            $table->index('student_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
