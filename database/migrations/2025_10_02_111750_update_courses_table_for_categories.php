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
        Schema::table('courses', function (Blueprint $table) {
            // Add bilingual name fields
            $table->string('name_ar')->nullable()->after('name');
            $table->string('name_en')->nullable()->after('name_ar');
            $table->text('description_ar')->nullable()->after('description');
            $table->text('description_en')->nullable()->after('description_ar');
            
            // Add category relationship
            $table->foreignId('category_id')->nullable()->after('description_en')->constrained('categories')->onDelete('set null');
            
            // Keep department for backward compatibility, but it will reference category
            $table->string('department')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['name_ar', 'name_en', 'description_ar', 'description_en', 'category_id']);
        });
    }
};
