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
        Schema::create('sales_targets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('category_id')->nullable(); // Department (optional, for department-specific targets)
            $table->string('period_type')->default('monthly'); // monthly, quarterly, yearly
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('target_amount', 10, 2);
            $table->decimal('achieved_amount', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            
            // Ensure one active target per user per period
            $table->unique(['user_id', 'start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_targets');
    }
};
