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
        Schema::table('course_classes', function (Blueprint $table) {
            $table->unsignedBigInteger('moodle_course_id')->nullable()->unique()->after('instructor_name');
            $table->index('moodle_course_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_classes', function (Blueprint $table) {
            $table->dropIndex(['moodle_course_id']);
            $table->dropColumn('moodle_course_id');
        });
    }
};
