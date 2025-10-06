@extends('layouts.app')

@section('title', __('enrollments.enrollment_details') . ' - CRM Academy')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="d-flex align-items-center">
                <a href="{{ route('students.show', $enrollment->student) }}" class="btn btn-outline-secondary me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-graduation-cap me-2"></i>
                        {{ __('enrollments.enrollment_details') }}
                    </h1>
                    <small class="text-muted">#{{ $enrollment->id }}</small>
                </div>
            </div>
            <div>
                @if($enrollment->is_active)
                    <span class="badge bg-success">{{ __('common.active') }}</span>
                @else
                    <span class="badge bg-secondary">{{ __('common.inactive') }}</span>
                @endif
                
                @if($enrollment->payment_status == 'completed')
                    <span class="badge bg-success">{{ __('enrollments.completed') }}</span>
                @elseif($enrollment->payment_status == 'partial')
                    <span class="badge bg-warning">{{ __('enrollments.partial') }}</span>
                @else
                    <span class="badge bg-danger">{{ __('enrollments.not_paid') }}</span>
                @endif
            </div>
        </div>

        <!-- Student Information Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user me-2"></i>
                    {{ __('students.student_information') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="fw-bold">{{ __('students.full_name') }}:</td>
                                <td>{{ $enrollment->student->full_name }}</td>
                            </tr>
                            @if($enrollment->student->full_name_en)
                            <tr>
                                <td class="fw-bold">{{ __('students.full_name_en') }}:</td>
                                <td>{{ $enrollment->student->full_name_en }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td class="fw-bold">{{ __('students.student_id') }}:</td>
                                <td><span class="badge bg-info">{{ $enrollment->student->student_id }}</span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="fw-bold">{{ __('students.phone_primary') }}:</td>
                                <td>{{ $enrollment->student->formatted_phone_primary }}</td>
                            </tr>
                            @if($enrollment->student->departmentCategory)
                            <tr>
                                <td class="fw-bold">{{ __('common.department') }}:</td>
                                <td>{{ $enrollment->student->departmentCategory->name }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course and Class Information Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-book me-2"></i>
                    {{ __('courses.course_information') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="fw-bold">{{ __('courses.course_name') }}:</td>
                                <td>{{ $enrollment->courseClass->course->name }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">{{ __('classes.class_name') }}:</td>
                                <td>{{ $enrollment->courseClass->class_name }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">{{ __('classes.class_code') }}:</td>
                                <td><span class="badge bg-primary">{{ $enrollment->courseClass->class_code }}</span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="fw-bold">{{ __('classes.start_date') }}:</td>
                                <td>{{ $enrollment->courseClass->start_date ? $enrollment->courseClass->start_date->format('M d, Y') : __('common.tbd') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">{{ __('classes.end_date') }}:</td>
                                <td>{{ $enrollment->courseClass->end_date ? $enrollment->courseClass->end_date->format('M d, Y') : __('common.tbd') }}</td>
                            </tr>
                            @if($enrollment->courseClass->instructor_name)
                            <tr>
                                <td class="fw-bold">{{ __('classes.instructor') }}:</td>
                                <td>{{ $enrollment->courseClass->instructor_name }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enrollment Details Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-clipboard-list me-2"></i>
                    {{ __('enrollments.enrollment_details') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="fw-bold">{{ __('enrollments.enrollment_date') }}:</td>
                                <td>{{ $enrollment->enrollment_date->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">{{ __('enrollments.total_amount') }}:</td>
                                <td class="fw-bold text-primary">{{ number_format($enrollment->total_amount, 2) }} {{ __('common.currency') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">{{ __('enrollments.paid_amount') }}:</td>
                                <td class="fw-bold text-success">{{ number_format($enrollment->paid_amount, 2) }} {{ __('common.currency') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">{{ __('enrollments.due_amount') }}:</td>
                                <td class="fw-bold text-danger">{{ number_format($enrollment->due_amount, 2) }} {{ __('common.currency') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="fw-bold">{{ __('enrollments.payment_status') }}:</td>
                                <td>
                                    @if($enrollment->payment_status == 'completed')
                                        <span class="badge bg-success">{{ __('enrollments.completed') }}</span>
                                    @elseif($enrollment->payment_status == 'partial')
                                        <span class="badge bg-warning">{{ __('enrollments.partial') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('enrollments.not_paid') }}</span>
                                    @endif
                                </td>
                            </tr>
                            @if($enrollment->notes)
                            <tr>
                                <td class="fw-bold">{{ __('enrollments.notes') }}:</td>
                                <td>{{ $enrollment->notes }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment History Card -->
        @if($enrollment->payments->count() > 0)
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-money-bill-wave me-2"></i>
                    {{ __('payments.payment_history') }}
                </h5>
                <span class="badge bg-primary">{{ $enrollment->payments->count() }} {{ __('payments.payments') }}</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('payments.payment_date') }}</th>
                                <th>{{ __('payments.amount') }}</th>
                                <th>{{ __('payments.payment_method') }}</th>
                                <th>{{ __('payments.status') }}</th>
                                <th>{{ __('payments.notes') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($enrollment->payments as $payment)
                            <tr>
                                <td>{{ $payment->payment_date ? $payment->payment_date->format('M d, Y') : '-' }}</td>
                                <td class="fw-bold text-success">{{ number_format($payment->amount, 2) }} {{ __('common.currency') }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ __('enrollments.' . $payment->payment_method) }}</span>
                                </td>
                                <td>
                                    @if($payment->status == 'completed')
                                        <span class="badge bg-success">{{ __('payments.completed') }}</span>
                                    @else
                                        <span class="badge bg-warning">{{ $payment->status }}</span>
                                    @endif
                                </td>
                                <td>{{ $payment->notes ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Actions Card -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <a href="{{ route('students.show', $enrollment->student) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            {{ __('common.back_to_student') }}
                        </a>
                    </div>
                    <div>
                        @if($enrollment->due_amount > 0)
                            <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                                <i class="fas fa-plus me-1"></i>
                                {{ __('enrollments.add_payment') }}
                            </button>
                        @endif
                        
                        @if($enrollment->is_active)
                            <form method="POST" action="{{ route('enrollments.destroy', $enrollment) }}" style="display: inline;" onsubmit="return confirm('{{ __('common.confirm_delete') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash me-1"></i>
                                    {{ __('common.delete') }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Payment Modal -->
@if($enrollment->due_amount > 0)
<div class="modal fade" id="addPaymentModal" tabindex="-1" aria-labelledby="addPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPaymentModalLabel">{{ __('enrollments.add_payment') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="#" id="addPaymentForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="payment_amount" class="form-label">{{ __('payments.amount') }} ({{ __('common.currency') }})</label>
                        <input type="number" class="form-control" id="payment_amount" name="amount" 
                               max="{{ $enrollment->due_amount }}" min="0.01" step="0.01" required>
                        <div class="form-text">{{ __('payments.maximum_amount') }}: {{ number_format($enrollment->due_amount, 2) }} {{ __('common.currency') }}</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">{{ __('enrollments.payment_method') }}</label>
                        <select class="form-select" id="payment_method" name="payment_method" required>
                            <option value="cash">{{ __('enrollments.cash') }}</option>
                            <option value="bank_transfer">{{ __('enrollments.bank_transfer') }}</option>
                            <option value="credit_card">{{ __('enrollments.credit_card') }}</option>
                            <option value="check">{{ __('enrollments.check') }}</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_date" class="form-label">{{ __('payments.payment_date') }}</label>
                        <input type="date" class="form-control" id="payment_date" name="payment_date" 
                               value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_notes" class="form-label">{{ __('payments.notes') }}</label>
                        <textarea class="form-control" id="payment_notes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="submit" class="btn btn-success">{{ __('payments.add_payment') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection