@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>{{ __('classes.class_details') }}: {{ $class->class_name }}</h2>
                <div class="btn-group">
                    <a href="{{ route('classes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-1"></i>
                        {{ __('classes.back_to_list') }}
                    </a>
                    <a href="{{ route('classes.edit', $class) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i>
                        {{ __('common.edit') }}
                    </a>
                </div>
            </div>

            <!-- Class Information Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">معلومات الشعبة (Class Information)</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">اسم الشعبة (Class Name):</th>
                                    <td>{{ $class->class_name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('classes.class_code') }}:</th>
                                    <td><code>{{ $class->class_code }}</code></td>
                                </tr>
                                <tr>
                                    <th>{{ __('classes.course') }}:</th>
                                    <td>{{ $class->course->name_ar ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('classes.department') }}:</th>
                                    <td><span class="badge bg-secondary">{{ $class->category->name }}</span></td>
                                </tr>
                                <tr>
                                    <th>{{ __('classes.instructor') }}:</th>
                                    <td>{{ $class->instructor_name ?? __('common.not_specified') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">{{ __('classes.start_date') }}:</th>
                                    <td>{{ $class->start_date->format('Y-m-d') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('classes.end_date') }}:</th>
                                    <td>{{ $class->end_date->format('Y-m-d') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('classes.class_fee') }}:</th>
                                    <td><strong>{{ number_format($class->default_price ?? 0, 0) }} JOD</strong></td>
                                </tr>
                                <tr>
                                    <th>{{ __('classes.max_students') }}:</th>
                                    <td>{{ $class->max_students ?? __('classes.unlimited') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('common.status') }}:</th>
                                    <td><span class="badge bg-{{ $class->status_color }}">{{ $class->status_label }}</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    @if($class->description)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>{{ __('classes.description') }}:</h6>
                            <p class="text-muted">{{ $class->description }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Financial Summary -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h4>{{ $class->total_enrolled_students }}</h4>
                            <p class="mb-0">{{ __('classes.enrolled_students') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h4>{{ number_format($class->total_required_amount, 0) }} JOD</h4>
                            <p class="mb-0">{{ __('classes.required_amount') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h4>{{ number_format($class->total_paid_amount, 0) }} JOD</h4>
                            <p class="mb-0">{{ __('classes.paid_amount') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h4>{{ number_format($class->total_due_amount, 0) }} JOD</h4>
                            <p class="mb-0">{{ __('classes.due_amount') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enrolled Students -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('classes.enrolled_students') }}</h5>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#enrollStudentModal">
                        <i class="fas fa-plus me-1"></i>
                        {{ __('classes.enroll_student') }}
                    </button>
                </div>
                <div class="card-body">
                    @if($class->enrollments->where('is_active', true)->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>{{ __('students.name') }}</th>
                                        <th>{{ __('classes.enrollment_date') }}</th>
                                        <th>{{ __('classes.total_amount') }}</th>
                                        <th>{{ __('classes.paid_amount') }}</th>
                                        <th>{{ __('classes.due_amount') }}</th>
                                        <th>{{ __('classes.payment_status') }}</th>
                                        <th>{{ __('common.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($class->enrollments->where('is_active', true) as $enrollment)
                                    <tr>
                                        <td>
                                            <strong>{{ $enrollment->student->full_name ?? 'N/A' }}</strong>
                                            <br><small class="text-muted">{{ $enrollment->student->formatted_phone_primary ?? '' }}</small>
                                        </td>
                                        <td>{{ $enrollment->enrollment_date->format('Y-m-d') }}</td>
                                        <td>{{ number_format($enrollment->total_amount, 0) }} JOD</td>
                                        <td>{{ number_format($enrollment->paid_amount, 0) }} JOD</td>
                                        <td>{{ number_format($enrollment->due_amount, 0) }} JOD</td>
                                        <td>
                                            @if($enrollment->payment_status === 'paid')
                                                <span class="badge bg-success">{{ __('classes.paid') }}</span>
                                            @elseif($enrollment->payment_status === 'partial')
                                                <span class="badge bg-warning">{{ __('classes.partial') }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ __('classes.unpaid') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary" title="{{ __('classes.view_payments') }}">
                                                    <i class="fas fa-money-bill-wave"></i>
                                                </button>
                                                @if($enrollment->payment_status !== 'completed' && $enrollment->due_amount > 0)
                                                    <button class="btn btn-outline-success add-payment-btn" 
                                                            title="{{ __('classes.add_payment') }}"
                                                            data-enrollment-id="{{ $enrollment->id }}"
                                                            data-student-name="{{ $enrollment->student->full_name ?? $enrollment->student->name }}"
                                                            data-due-amount="{{ $enrollment->due_amount }}"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#addPaymentModal">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-user-graduate fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">{{ __('classes.no_enrolled_students') }}</h5>
                            <p class="text-muted">{{ __('classes.start_enrolling_students') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payment History -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('classes.payment_history') }}</h5>
                </div>
                <div class="card-body">
                    @php
                        $payments = $class->enrollments->flatMap->payments()->sortByDesc('payment_date');
                    @endphp
                    
                    @if($payments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>{{ __('classes.date') }}</th>
                                        <th>{{ __('students.student') }}</th>
                                        <th>{{ __('classes.amount') }}</th>
                                        <th>{{ __('classes.payment_method') }}</th>
                                        <th>{{ __('classes.notes') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments->take(10) as $payment)
                                    <tr>
                                        <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                                        <td>{{ $payment->enrollment->student->name ?? 'N/A' }}</td>
                                        <td>{{ number_format($payment->amount, 0) }} JOD</td>
                                        <td>{{ $payment->payment_method_label }}</td>
                                        <td>{{ $payment->notes ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if($payments->count() > 10)
                            <div class="text-center mt-3">
                                <small class="text-muted">{{ __('classes.showing_payments', ['current' => 10, 'total' => $payments->count()]) }}</small>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-receipt fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">{{ __('classes.no_payments_yet') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Payment Modal -->
<div class="modal fade" id="addPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-dollar-sign me-2"></i>
                    {{ __('classes.add_payment') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="" id="addPaymentForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('students.student') }}</label>
                        <input type="text" class="form-control" id="studentName" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_amount" class="form-label">
                            {{ __('students.payment_amount') }} ({{ __('common.currency') }}) <span class="text-danger">*</span>
                        </label>
                        <input type="number" class="form-control" id="payment_amount" name="amount" 
                               min="0.01" step="0.01" required>
                        <div class="form-text" id="dueAmountHelper">
                            {{ __('payments.maximum_amount') }}: <span id="maxAmount"></span> {{ __('common.currency') }}
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment_method" class="form-label">
                                    {{ __('students.payment_method') }} <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="payment_method" name="payment_method" required>
                                    <option value="cash">{{ __('payments.cash') }}</option>
                                    <option value="bank_transfer">{{ __('payments.bank_transfer') }}</option>
                                    <option value="credit_card">{{ __('payments.credit_card') }}</option>
                                    <option value="check">{{ __('payments.check') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment_date" class="form-label">
                                    {{ __('students.payment_date') }} <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control" id="payment_date" name="payment_date" 
                                       value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">{{ __('students.notes') }}</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" 
                                  placeholder="{{ __('students.payment_notes_placeholder') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ __('common.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-plus me-1"></i>
                        {{ __('students.add_payment') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Enroll Student Modal -->
<div class="modal fade" id="enrollStudentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('classes.enroll_new_student') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="#" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="student_id" class="form-label">{{ __('classes.select_student') }}</label>
                        <select class="form-select" id="student_id" name="student_id" required>
                            <option value="">{{ __('classes.choose_student') }}</option>
                            <!-- Students will be loaded via AJAX or from controller -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="enrollment_date" class="form-label">{{ __('classes.enrollment_date') }}</label>
                        <input type="date" class="form-control" id="enrollment_date" name="enrollment_date" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="total_amount" class="form-label">{{ __('classes.total_amount') }}</label>
                        <input type="number" class="form-control" id="total_amount" name="total_amount" value="{{ $class->default_price ?? 0 }}" step="0.01" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('classes.enroll_student') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Payment modal functionality
    const addPaymentButtons = document.querySelectorAll('.add-payment-btn');
    const addPaymentForm = document.getElementById('addPaymentForm');
    const studentNameInput = document.getElementById('studentName');
    const paymentAmountInput = document.getElementById('payment_amount');
    const maxAmountSpan = document.getElementById('maxAmount');
    
    addPaymentButtons.forEach(button => {
        button.addEventListener('click', function() {
            const enrollmentId = this.getAttribute('data-enrollment-id');
            const studentName = this.getAttribute('data-student-name');
            const dueAmount = parseFloat(this.getAttribute('data-due-amount'));
            
            // Set form action
            addPaymentForm.action = `{{ url('enrollments') }}/${enrollmentId}/payments`;
            
            // Set student name
            studentNameInput.value = studentName;
            
            // Set max amount
            maxAmountSpan.textContent = dueAmount.toFixed(2);
            paymentAmountInput.max = dueAmount;
            paymentAmountInput.value = '';
        });
    });
    
    // Validate payment amount
    paymentAmountInput.addEventListener('input', function() {
        const amount = parseFloat(this.value);
        const maxAmount = parseFloat(this.max);
        
        if (amount > maxAmount) {
            this.setCustomValidity('{{ __("payments.amount_exceeds_due_amount_js") }}');
        } else {
            this.setCustomValidity('');
        }
    });
});
</script>

@endsection