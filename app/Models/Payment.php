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
        'currency_code',
        'amount_in_jod',
        'exchange_rate',
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
        'amount_in_jod' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
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

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_code', 'code');
    }

    // Accessors
    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'cash' => __('payments.cash'),
            'bank_transfer' => __('payments.bank_transfer'),
            'credit_card' => __('payments.credit_card'),
            'check' => __('payments.check'),
            'zaincash' => __('payments.zaincash'),
            'other' => __('payments.other'),
            default => $this->payment_method
        };
    }

    public function getFormattedAmountAttribute(): string
    {
        $currencySymbol = $this->currency->symbol ?? '';
        return number_format($this->amount, 2) . ' ' . $currencySymbol . ' (' . $this->currency_code . ')';
    }

    public function getFormattedAmountInJodAttribute(): string
    {
        return number_format($this->amount_in_jod, 2) . ' JD';
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
