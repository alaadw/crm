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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique(); // JOD, USD, ILS
            $table->string('name'); // Jordanian Dinar, US Dollar, Israeli Shekel
            $table->string('name_ar'); // دينار أردني, دولار أمريكي, شيكل إسرائيلي
            $table->string('symbol', 10); // JD, $, ₪
            $table->decimal('exchange_rate_to_jod', 10, 4)->default(1); // Rate to convert to JOD
            $table->boolean('is_active')->default(true);
            $table->boolean('is_base_currency')->default(false); // JOD is base
            $table->timestamps();
        });

        // Insert default currencies
        DB::table('currencies')->insert([
            [
                'code' => 'JOD',
                'name' => 'Jordanian Dinar',
                'name_ar' => 'دينار أردني',
                'symbol' => 'JD',
                'exchange_rate_to_jod' => 1.0000,
                'is_active' => true,
                'is_base_currency' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'USD',
                'name' => 'US Dollar',
                'name_ar' => 'دولار أمريكي',
                'symbol' => '$',
                'exchange_rate_to_jod' => 0.7070, // 1 USD = 0.707 JOD
                'is_active' => true,
                'is_base_currency' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'ILS',
                'name' => 'Israeli Shekel',
                'name_ar' => 'شيكل إسرائيلي',
                'symbol' => '₪',
                'exchange_rate_to_jod' => 0.1900, // 1 ILS = 0.19 JOD
                'is_active' => true,
                'is_base_currency' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
