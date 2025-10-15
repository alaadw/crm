<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'code',
        'name',
        'name_ar',
        'symbol',
        'exchange_rate_to_jod',
        'is_active',
        'is_base_currency',
    ];

    protected $casts = [
        'exchange_rate_to_jod' => 'decimal:4',
        'is_active' => 'boolean',
        'is_base_currency' => 'boolean',
    ];

    /**
     * Get active currencies
     */
    public static function getActiveCurrencies()
    {
        return static::where('is_active', true)
            ->orderBy('is_base_currency', 'desc')
            ->orderBy('code')
            ->get();
    }

    /**
     * Get base currency (JOD)
     */
    public static function getBaseCurrency()
    {
        return static::where('is_base_currency', true)->first();
    }

    /**
     * Convert amount to JOD
     */
    public function convertToJOD($amount)
    {
        return $amount * $this->exchange_rate_to_jod;
    }

    /**
     * Convert amount from JOD to this currency
     */
    public function convertFromJOD($amountInJOD)
    {
        return $this->exchange_rate_to_jod > 0 
            ? $amountInJOD / $this->exchange_rate_to_jod 
            : 0;
    }

    /**
     * Get display name based on locale
     */
    public function getDisplayNameAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name;
    }
}
