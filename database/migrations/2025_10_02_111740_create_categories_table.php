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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name_en')->nullable();
            $table->string('name_ar', 200)->nullable();
            $table->string('order', 20)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->unsignedBigInteger('parent_id')->default(0);
            $table->text('description')->nullable();
            $table->text('keywords')->nullable();
            $table->tinyInteger('show')->default(1);
            $table->string('name_alias', 30)->nullable();
            $table->string('img')->nullable();
            $table->timestamps();
            
            // Add indexes
            $table->index('parent_id');
            $table->index('status');
            $table->index('show');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
