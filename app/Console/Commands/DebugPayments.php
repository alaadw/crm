<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payment;
use App\Models\Enrollment;

class DebugPayments extends Command
{
    protected $signature = 'debug:payments';
    protected $description = 'Debug recent payments and enrollment updates';

    public function handle()
    {
        $this->info('=== Recent Payments ===');
        $payments = Payment::with('enrollment')->latest()->take(5)->get();
        
        foreach ($payments as $payment) {
            $this->line("Payment #{$payment->id}:");
            $this->line("  Amount: {$payment->amount} {$payment->currency_code}");
            $this->line("  Amount in JOD (raw): {$payment->getRawOriginal('amount_in_jod')}");
            $this->line("  Amount in JOD (accessor): {$payment->amount_in_jod}");
            $this->line("  Exchange Rate: {$payment->exchange_rate}");
            $this->line("  Enrollment ID: {$payment->enrollment_id}");
            $this->line("  Enrollment Paid: {$payment->enrollment->paid_amount} JOD");
            $this->line("  Enrollment Due: {$payment->enrollment->due_amount} JOD");
            $this->line("  Enrollment Total: {$payment->enrollment->total_amount} JOD");
            $this->line("  Payment Date: {$payment->payment_date}");
            $this->line("  Created At: {$payment->created_at}");
            $this->line('---');
        }
        
        // Also check sum of payments for enrollment 5
        $this->info('=== Enrollment 5 Analysis ===');
        $enrollment = \App\Models\Enrollment::find(5);
        if ($enrollment) {
            $paymentsSum = \App\Models\Payment::where('enrollment_id', 5)->sum('amount_in_jod');
            $this->line("Enrollment Total: {$enrollment->total_amount} JOD");
            $this->line("Enrollment Paid Amount: {$enrollment->paid_amount} JOD");
            $this->line("Sum of Payments (amount_in_jod): {$paymentsSum} JOD");
            $this->line("Difference: " . ($enrollment->paid_amount - $paymentsSum) . " JOD");
        }
        
        return 0;
    }
}
