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
        Schema::table('payments', function (Blueprint $table) {
            $table->string('currency_code', 3)->default('JOD')->after('amount');
            $table->decimal('amount_in_jod', 10, 2)->after('currency_code');
            $table->decimal('exchange_rate', 10, 4)->default(1)->after('amount_in_jod');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['currency_code', 'amount_in_jod', 'exchange_rate']);
        });
    }
};
