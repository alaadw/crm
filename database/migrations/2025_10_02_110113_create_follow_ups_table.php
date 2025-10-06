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
        Schema::create('follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Employee responsible
            $table->text('action_note'); // The follow-up note
            $table->enum('outcome', [
                'Interested',
                'No Answer',
                'Wrong Number',
                'Not Interested',
                'Callback Requested',
                'Other'
            ])->nullable();
            $table->enum('status', [
                'No Follow-ups', // Default for new students
                'Postponed',     // أجّل
                'Expected to Register', // متوقع تسجيله
                'Registered',    // سجّل
                'Cancelled'      // ألغى
            ]);
            $table->date('next_follow_up_date')->nullable(); // Required for Postponed
            $table->enum('cancellation_reason', [
                'Price',
                'Time',
                'Registered Elsewhere',
                'Not Interested',
                'Other'
            ])->nullable(); // Required for Cancelled
            $table->text('cancellation_details')->nullable(); // Additional cancellation details
            $table->integer('priority')->default(3); // 1 = High, 2 = Medium, 3 = Low
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['student_id', 'created_at']);
            $table->index(['user_id', 'next_follow_up_date']);
            $table->index(['status', 'next_follow_up_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follow_ups');
    }
};
