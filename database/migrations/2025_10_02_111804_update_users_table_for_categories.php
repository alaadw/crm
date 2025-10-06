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
        Schema::table('users', function (Blueprint $table) {
            // Add category relationship for department
            $table->foreignId('department_category_id')->nullable()->after('department')->constrained('categories')->onDelete('set null');
            
            // Keep department for backward compatibility
            $table->string('department')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['department_category_id']);
            $table->dropColumn(['department_category_id']);
        });
    }
};
