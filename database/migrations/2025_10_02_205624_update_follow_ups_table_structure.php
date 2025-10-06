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
        Schema::table('follow_ups', function (Blueprint $table) {
            // Add new columns needed for the enhanced follow-up system
            $table->datetime('scheduled_date')->after('user_id'); // When the follow-up is scheduled
            $table->string('contact_method')->after('scheduled_date'); // phone, whatsapp, email, in_person
            $table->string('type')->after('contact_method'); // initial_contact, course_inquiry, etc.
            $table->text('purpose')->after('type'); // Purpose/agenda of the follow-up
            $table->text('notes')->nullable()->after('purpose'); // Additional notes
            
            // Update existing columns
            $table->string('priority', 10)->default('medium')->change(); // high, medium, low instead of integer
            
            // Update status enum to be more appropriate for scheduling
            $table->enum('status', [
                'pending',     // Scheduled but not completed yet
                'completed',   // Follow-up completed
                'cancelled',   // Follow-up cancelled
                'postponed'    // Postponed to later date
            ])->default('pending')->change();
            
            // Keep existing fields but make them nullable for backwards compatibility
            $table->text('action_note')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('follow_ups', function (Blueprint $table) {
            // Remove new columns
            $table->dropColumn(['scheduled_date', 'contact_method', 'type', 'purpose', 'notes']);
            
            // Revert changes
            $table->integer('priority')->default(3)->change();
            $table->enum('status', [
                'No Follow-ups',
                'Postponed',
                'Expected to Register',
                'Registered',
                'Cancelled'
            ])->change();
            $table->text('action_note')->nullable(false)->change();
        });
    }
};
