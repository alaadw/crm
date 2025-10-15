<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CourseClass;

class DebugClassPayments extends Command
{
    protected $signature = 'debug:class-payments {class_id}';
    protected $description = 'Debug class payments and student relationships';

    public function handle()
    {
        $classId = $this->argument('class_id');
        $class = CourseClass::with([
            'course', 
            'category', 
            'enrollments.student', 
            'enrollments.registeredBy',
            'enrollments.payments'
        ])->find($classId);
        
        if (!$class) {
            $this->error("Class #{$classId} not found");
            return 1;
        }
        
        $this->info("=== Class: {$class->class_name} ===");
        $this->line("Enrollments count: " . $class->enrollments->count());
        
        foreach ($class->enrollments as $enrollment) {
            $this->line("\nEnrollment #{$enrollment->id}:");
            $this->line("  Student: " . ($enrollment->student ? $enrollment->student->name : 'NO STUDENT'));
            $this->line("  Student ID: " . $enrollment->student_id);
            $this->line("  Payments count: " . $enrollment->payments->count());
            
            foreach ($enrollment->payments as $payment) {
                $this->line("    Payment #{$payment->id}:");
                $this->line("      Amount: {$payment->amount} {$payment->currency_code}");
                $this->line("      Date: {$payment->payment_date}");
                $this->line("      Enrollment ID: {$payment->enrollment_id}");
                
                // Test accessing student through payment->enrollment
                $enrollmentFromPayment = $payment->enrollment;
                if ($enrollmentFromPayment) {
                    $studentFromPayment = $enrollmentFromPayment->student;
                    $this->line("      Student via payment->enrollment: " . ($studentFromPayment ? $studentFromPayment->name : 'NO STUDENT'));
                } else {
                    $this->line("      No enrollment found for payment");
                }
            }
        }
        
        // Test flatMap approach
        $this->info("\n=== Testing FlatMap ===");
        $payments = $class->enrollments->flatMap(function($enrollment) {
            return $enrollment->payments->each(function($payment) use ($enrollment) {
                $payment->setRelation('enrollment', $enrollment);
            });
        });
        
        $this->line("Total payments after flatMap: " . $payments->count());
        
        foreach ($payments->take(3) as $payment) {
            $this->line("Payment #{$payment->id}:");
            $this->line("  Amount: {$payment->amount} {$payment->currency_code}");
            $this->line("  Student: " . ($payment->enrollment && $payment->enrollment->student ? $payment->enrollment->student->name : 'NO STUDENT'));
        }
        
        return 0;
    }
}