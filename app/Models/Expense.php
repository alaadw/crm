<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_type_id',
        'amount',
        'date',
        'department_category_id',
        'added_by_user_id',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:3',
        'date' => 'date',
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(ExpenseType::class, 'expense_type_id');
    }

    public function departmentCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'department_category_id');
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by_user_id');
    }
}
