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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('enrollment_id');
            $table->unsignedBigInteger('received_by'); // User who received the payment
            $table->decimal('amount', 10, 2);
            $table->date('payment_date');
            $table->enum('payment_method', ['cash', 'bank_transfer', 'credit_card', 'check', 'other'])->default('cash');
            $table->string('reference_number')->nullable(); // Transaction reference or receipt number
            $table->text('notes')->nullable();
            $table->boolean('is_refunded')->default(false);
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->date('refund_date')->nullable();
            $table->text('refund_reason')->nullable();
            $table->timestamps();
            
            $table->foreign('enrollment_id')->references('id')->on('enrollments')->onDelete('cascade');
            $table->foreign('received_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
