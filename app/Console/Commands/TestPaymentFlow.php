<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Enrollment;
use App\Services\EnrollmentService;

class TestPaymentFlow extends Command
{
    protected $signature = 'test:payment-flow';
    protected $description = 'Test payment flow step by step';

    public function handle(EnrollmentService $enrollmentService)
    {
        $this->info('=== Testing Payment Flow ===');
        
        // Find an enrollment with due amount
        $enrollment = Enrollment::where('due_amount', '>', 0)->first();
        
        if (!$enrollment) {
            $this->error('No enrollment with due amount found');
            return 1;
        }
        
        $this->info("Testing with Enrollment #{$enrollment->id}");
        $this->line("Student: " . ($enrollment->student ? $enrollment->student->name : 'N/A'));
        $this->line("BEFORE payment:");
        $this->line("  Total: {$enrollment->total_amount} JOD");
        $this->line("  Paid: {$enrollment->paid_amount} JOD");
        $this->line("  Due: {$enrollment->due_amount} JOD");
        
        // Check existing payments
        $existingPayments = $enrollment->payments;
        $this->line("  Existing payments: {$existingPayments->count()}");
        $totalPaid = $existingPayments->sum('amount_in_jod');
        $this->line("  Sum of amount_in_jod: {$totalPaid} JOD");
        
        if ($totalPaid != $enrollment->paid_amount) {
            $this->warn("  ⚠️  Mismatch! Enrollment paid_amount ({$enrollment->paid_amount}) != sum of payments ({$totalPaid})");
        }
        
        // Add a 10 ILS payment (should be ~1.9 JOD at 0.19 rate)
        $this->info("\nAdding 10 ILS payment...");
        
        try {
            $payment = $enrollmentService->addPayment($enrollment->id, [
                'amount' => 10,
                'currency_code' => 'ILS',
                'payment_method' => 'cash',
                'payment_date' => now(),
                'notes' => 'Test payment flow',
            ]);
            
            $this->info("✅ Payment added successfully!");
            $this->line("Payment details:");
            $this->line("  ID: #{$payment->id}");
            $this->line("  Amount: {$payment->amount} {$payment->currency_code}");
            $this->line("  Amount in JOD: {$payment->amount_in_jod}");
            $this->line("  Exchange Rate: {$payment->exchange_rate}");
            
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }
        
        // Refresh enrollment and check after
        $enrollment->refresh();
        
        $this->info("\nAFTER payment:");
        $this->line("  Total: {$enrollment->total_amount} JOD");
        $this->line("  Paid: {$enrollment->paid_amount} JOD");
        $this->line("  Due: {$enrollment->due_amount} JOD");
        $this->line("  Status: {$enrollment->payment_status}");
        
        // Check all payments again
        $allPayments = $enrollment->payments()->get();
        $newTotalPaid = $allPayments->sum('amount_in_jod');
        $this->line("  All payments count: {$allPayments->count()}");
        $this->line("  New sum of amount_in_jod: {$newTotalPaid} JOD");
        
        if ($newTotalPaid != $enrollment->paid_amount) {
            $this->warn("  ⚠️  Still mismatched! Enrollment paid_amount ({$enrollment->paid_amount}) != sum of payments ({$newTotalPaid})");
        } else {
            $this->info("  ✅ Amounts match correctly!");
        }
        
        return 0;
    }
}