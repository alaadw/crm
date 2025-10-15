<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payment;

class FixOldPayments extends Command
{
    protected $signature = 'fix:old-payments';
    protected $description = 'Fix old payments that have amount_in_jod = 0';

    public function handle()
    {
        $this->info('=== Fixing Old Payments ===');
        
        // Find payments with amount_in_jod = 0 and currency_code = JOD
        $oldPayments = Payment::where('amount_in_jod', 0)
                             ->where('currency_code', 'JOD')
                             ->get();
        
        $this->line("Found {$oldPayments->count()} old JOD payments with amount_in_jod = 0");
        
        if ($oldPayments->count() === 0) {
            $this->info('No payments to fix!');
            return 0;
        }
        
        $this->line('Payments to fix:');
        foreach ($oldPayments as $payment) {
            $this->line("  Payment #{$payment->id}: {$payment->amount} JOD (enrollment {$payment->enrollment_id})");
        }
        
        if (!$this->confirm('Do you want to fix these payments?')) {
            $this->info('Cancelled.');
            return 0;
        }
        
        $fixed = 0;
        foreach ($oldPayments as $payment) {
            // For JOD payments, amount_in_jod should equal amount
            $payment->update([
                'amount_in_jod' => $payment->amount,
                'exchange_rate' => 1.0000
            ]);
            
            $fixed++;
            $this->line("✅ Fixed payment #{$payment->id}: set amount_in_jod to {$payment->amount}");
        }
        
        $this->info("🎉 Fixed {$fixed} payments successfully!");
        $this->info('Now all payment totals should match enrollment amounts.');
        
        return 0;
    }
}