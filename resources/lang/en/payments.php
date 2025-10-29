<?php

return [
    // Payments
    'payments' => 'Payments',
    'payment' => 'Payment',
    'payment_history' => 'Payment History',
    'payment_date' => 'Payment Date',
    'payment_method' => 'Payment Method',
    'currency_code' => 'Currency',
    'amount' => 'Amount',
    'amount_jod' => 'Amount (JOD)',
    'status' => 'Status',
    'notes' => 'Notes',
    'add_payment' => 'Add Payment',
    'maximum_amount' => 'Maximum amount',
    'completed' => 'Completed',
    'pending' => 'Pending',
    'cancelled' => 'Cancelled',
    
    // Payment Methods
    'cash' => 'Cash',
    'bank_transfer' => 'Bank Transfer',
    'credit_card' => 'Credit Card',
    'check' => 'Check',
    'zaincash' => 'ZainCash',
    'other' => 'Other',
    
    // Messages
    'payment_added_successfully' => 'Payment added successfully',
    'enrollment_not_found_for_student' => 'The selected enrollment does not belong to this student',
    'enrollment_not_active' => 'This enrollment is not active',
    'amount_exceeds_due_amount' => 'Payment amount cannot exceed due amount of :due',
    'amount_exceeds_due_amount_js' => 'Payment amount cannot exceed the due amount',
    
    // Validation messages
    'enrollment_required' => 'Please select an enrollment.',
    'enrollment_invalid' => 'Please select a valid enrollment.',
    'amount_required' => 'Payment amount is required.',
    'amount_numeric' => 'Payment amount must be a number.',
    'amount_min' => 'Payment amount must be at least 0.01.',
    'payment_method_required' => 'Please select a payment method.',
    'payment_method_invalid' => 'Please select a valid payment method.',
    'payment_date_required' => 'Payment date is required.',
    'payment_date_invalid' => 'Please enter a valid payment date.',
    'notes_max' => 'Notes may not be greater than 1000 characters.',
    
    // Field names
    'enrollment' => 'Enrollment',

    // Listing & charts
    'statistics' => 'Payment Statistics',
    'chart_type' => 'Chart Type',
    'by_time' => 'By Time',
    'by_method' => 'By Method',
    'period' => 'Period',
    'day' => 'Day',
    'week' => 'Week',
    'month' => 'Month',
    'sales_rep' => 'Sales Representative',
    'received_by' => 'Received By',
    'class' => 'Class',
    'total_amount' => 'Total Collected',
    'total_by_period' => 'Total by period',
    'total_by_method' => 'Total by method',
    'week_label' => 'Week :week',
    'print_now' => 'Print now',
    'export_excel_filename' => 'payments_export_:date.xlsx',
    'export_pdf_filename' => 'payments_export_:date.pdf',
];