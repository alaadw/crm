<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_en',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    // Localized name accessor
    public function getDisplayNameAttribute(): string
    {
        $locale = app()->getLocale();
        if ($locale === 'ar' && !empty($this->name)) {
            return $this->name;
        }
        if ($locale !== 'ar' && !empty($this->name_en)) {
            return $this->name_en;
        }
        // Fallbacks
        return $this->name_en ?: $this->name ?: '';
    }
}
