<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'received_by',
        'amount',
        'payment_date',
        'payment_method',
        'reference_number',
        'notes',
        'is_refunded',
        'refund_amount',
        'refund_date',
        'refund_reason',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'is_refunded' => 'boolean',
        'refund_amount' => 'decimal:2',
        'refund_date' => 'date',
    ];

    // Relationships
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    // Accessors
    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'cash' => 'نقدي',
            'bank_transfer' => 'تحويل بنكي',
            'credit_card' => 'بطاقة ائتمان',
            'check' => 'شيك',
            'other' => 'أخرى',
            default => $this->payment_method
        };
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2) . ' JOD';
    }

    // Scopes
    public function scopeNotRefunded($query)
    {
        return $query->where('is_refunded', false);
    }

    public function scopeByMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }
}
