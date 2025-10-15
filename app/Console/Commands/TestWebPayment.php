<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Enrollment;
use App\Http\Controllers\PaymentController;
use App\Http\Requests\StorePaymentRequest;
use Illuminate\Http\Request;

class TestWebPayment extends Command
{
    protected $signature = 'test:web-payment {enrollment_id}';
    protected $description = 'Test payment submission like from web interface';

    public function handle()
    {
        $enrollmentId = $this->argument('enrollment_id');
        $enrollment = Enrollment::find($enrollmentId);
        
        if (!$enrollment) {
            $this->error("Enrollment #{$enrollmentId} not found");
            return 1;
        }
        
        $this->info("=== Testing Web Payment Submission ===");
        $this->line("Enrollment #{$enrollment->id}");
        $this->line("Student: " . ($enrollment->student ? $enrollment->student->name : 'N/A'));
        $this->line("Before - Due: {$enrollment->due_amount} JOD");
        
        // Simulate form data that would come from the web interface
        $formData = [
            'amount' => 5.00,
            'currency_code' => 'ILS', 
            'payment_method' => 'cash',
            'payment_date' => now()->format('Y-m-d'),
            'notes' => 'Test web payment',
        ];
        
        $this->info("Submitting payment: 5 ILS (should be ~0.95 JOD)");
        
        // Create a mock request like what would come from the form
        $request = new Request($formData);
        $request->setMethod('POST');
        
        try {
            // Test the payment controller method directly
            $controller = app(PaymentController::class);
            
            // We can't easily test the form request validation here, so let's test the service directly
            $enrollmentService = app(\App\Services\EnrollmentService::class);
            
            $result = $enrollmentService->handlePaymentAddition($enrollment->id, $formData);
            
            if ($result['success']) {
                $this->info("✅ Payment submitted successfully!");
                $this->line("Message: " . $result['message']);
                
                // Refresh and check
                $enrollment->refresh();
                $this->line("After - Due: {$enrollment->due_amount} JOD");
                $this->line("After - Paid: {$enrollment->paid_amount} JOD");
                
                // Check latest payment
                $latestPayment = $enrollment->payments()->latest()->first();
                if ($latestPayment) {
                    $this->line("Latest payment: {$latestPayment->amount} {$latestPayment->currency_code} = {$latestPayment->amount_in_jod} JOD");
                }
                
            } else {
                $this->error("❌ Payment failed!");
                $this->line("Error: " . $result['error']);
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Exception: " . $e->getMessage());
            $this->line("Stack trace: " . $e->getTraceAsString());
        }
        
        return 0;
    }
}