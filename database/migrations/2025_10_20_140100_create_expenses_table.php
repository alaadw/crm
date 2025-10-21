<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_type_id')->constrained('expense_types');
            $table->decimal('amount', 12, 3);
            $table->date('date');
            $table->foreignId('department_category_id')->constrained('categories');
            $table->foreignId('added_by_user_id')->constrained('users');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->index(['date', 'department_category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
