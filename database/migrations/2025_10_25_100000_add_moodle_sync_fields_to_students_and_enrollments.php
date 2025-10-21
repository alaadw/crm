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
        Schema::table('students', function (Blueprint $table) {
            $table->unsignedBigInteger('moodle_user_id')->nullable()->after('preferred_course_id');
            $table->timestamp('moodle_user_synced_at')->nullable()->after('moodle_user_id');
            $table->index('moodle_user_id');
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->string('moodle_sync_status', 32)->default('not_synced')->after('notes');
            $table->timestamp('moodle_enrolled_at')->nullable()->after('moodle_sync_status');
            $table->text('moodle_last_error')->nullable()->after('moodle_enrolled_at');
            $table->index('moodle_sync_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex(['moodle_user_id']);
            $table->dropColumn(['moodle_user_id', 'moodle_user_synced_at']);
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropIndex(['moodle_sync_status']);
            $table->dropColumn(['moodle_sync_status', 'moodle_enrolled_at', 'moodle_last_error']);
        });
    }
};
