<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Enrollment;
use App\Models\CourseClass;

class DebugFullFlow extends Command
{
    protected $signature = 'debug:full-flow';
    protected $description = 'Debug the complete payment flow including web interface data';

    public function handle()
    {
        $this->info('=== Full Payment Flow Debug ===');
        
        // Test with a specific class that has enrollments
        $class = CourseClass::with(['enrollments.student', 'enrollments.payments'])->first();
        
        if (!$class) {
            $this->error('No classes found');
            return 1;
        }
        
        $this->info("Testing Class: {$class->class_name} (ID: {$class->id})");
        $this->line("URL: http://127.0.0.1:8000/classes/{$class->id}");
        
        foreach ($class->enrollments as $enrollment) {
            $this->line("\n--- Enrollment #{$enrollment->id} ---");
            $this->line("Student: " . ($enrollment->student ? $enrollment->student->name : 'N/A'));
            $this->line("Total: {$enrollment->total_amount} JOD");
            $this->line("Paid: {$enrollment->paid_amount} JOD");
            $this->line("Due: {$enrollment->due_amount} JOD");
            $this->line("Status: {$enrollment->payment_status}");
            
            // Show payment history
            $this->line("Payments:");
            foreach ($enrollment->payments as $payment) {
                $this->line("  #{$payment->id}: {$payment->amount} {$payment->currency_code} = {$payment->amount_in_jod} JOD on {$payment->payment_date->format('Y-m-d')}");
            }
            
            // Check if this enrollment can receive payments
            if ($enrollment->due_amount > 0) {
                $this->info("✅ This enrollment can receive payments (due: {$enrollment->due_amount} JOD)");
                $this->line("Payment form URL: http://127.0.0.1:8000/enrollments/{$enrollment->id}/payments");
            } else {
                $this->line("⚪ This enrollment is fully paid");
            }
        }
        
        // Test payment history display (like in the blade file)
        $this->info("\n=== Payment History (as shown in web interface) ===");
        
        $payments = $class->enrollments->flatMap(function($enrollment) {
            return $enrollment->payments->each(function($payment) use ($enrollment) {
                $payment->setRelation('enrollment', $enrollment);
            });
        })->sortByDesc('payment_date');
        
        $this->line("Total payments in history: " . $payments->count());
        
        foreach ($payments->take(5) as $payment) {
            $studentName = $payment->enrollment && $payment->enrollment->student 
                ? $payment->enrollment->student->name 
                : 'N/A';
            
            $this->line("{$payment->payment_date->format('Y-m-d')} | {$studentName} | {$payment->formatted_amount} | {$payment->payment_method_label}");
        }
        
        return 0;
    }
}