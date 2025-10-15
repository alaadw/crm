<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EnrollmentService;
use App\Models\Enrollment;

class TestPayment extends Command
{
    protected $signature = 'test:payment {enrollment_id}';
    protected $description = 'Test adding a payment to an enrollment';

    public function handle(EnrollmentService $enrollmentService)
    {
        $enrollmentId = $this->argument('enrollment_id');
        $enrollment = Enrollment::find($enrollmentId);
        
        if (!$enrollment) {
            $this->error("Enrollment #{$enrollmentId} not found");
            return 1;
        }
        
        $this->info("=== BEFORE Payment ===");
        $this->line("Enrollment #{$enrollment->id}");
        $this->line("  Total Amount: {$enrollment->total_amount} JOD");
        $this->line("  Paid Amount: {$enrollment->paid_amount} JOD");
        $this->line("  Due Amount: {$enrollment->due_amount} JOD");
        $this->line("  Payment Status: {$enrollment->payment_status}");
        
        // Add a 25 ILS payment (should convert to ~4.75 JOD at 0.19 rate)
        $this->info("\nAdding payment: 25 ILS");
        
        try {
            $payment = $enrollmentService->addPayment($enrollmentId, [
                'amount' => 25,
                'currency_code' => 'ILS',
                'payment_method' => 'cash',
                'payment_date' => now(),
                'notes' => 'Test payment from command',
            ]);
            
            $this->info("Payment created successfully: #{$payment->id}");
            $this->line("  Amount: {$payment->amount} {$payment->currency_code}");
            $this->line("  Amount in JOD: {$payment->amount_in_jod}");
            $this->line("  Exchange Rate: {$payment->exchange_rate}");
            
        } catch (\Exception $e) {
            $this->error("Error adding payment: " . $e->getMessage());
            return 1;
        }
        
        // Refresh enrollment and show after
        $enrollment->refresh();
        
        $this->info("\n=== AFTER Payment ===");
        $this->line("Enrollment #{$enrollment->id}");
        $this->line("  Total Amount: {$enrollment->total_amount} JOD");
        $this->line("  Paid Amount: {$enrollment->paid_amount} JOD");
        $this->line("  Due Amount: {$enrollment->due_amount} JOD");
        $this->line("  Payment Status: {$enrollment->payment_status}");
        
        return 0;
    }
}
