<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Note: Since MySQL doesn't easily support modifying enums,
        // we'll alter the column to allow zaincash
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_method VARCHAR(50) DEFAULT 'cash'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back if needed
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_method VARCHAR(50) DEFAULT 'cash'");
    }
};
