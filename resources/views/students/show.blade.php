@extends('layouts.app')

@section('title', $student->full_name . ' - ' . __('students.student_profile'))

@section('content')
<div class="row">
    <div class="col-md-8">
        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="d-flex align-items-center">
                <a href="{{ route('students.index') }}" class="btn btn-outline-secondary me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="h3 mb-0">{{ $student->full_name }}</h1>
                    <small class="text-muted">{{ __('students.student_id') }}: <code>{{ $student->student_id }}</code></small>
                </div>
            </div>
            <div class="btn-group">
                <a href="{{ route('students.edit', $student) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i>
                    {{ __('students.edit_profile') }}
                </a>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="fas fa-trash me-1"></i>
                    {{ __('common.delete') }}
                </button>
            </div>
        </div>

        <!-- Student Information -->
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
                        <table class="table table-borderless">
                            <tr>
                                <td width="150"><strong>{{ __('students.full_name') }}:</strong></td>
                                <td>{{ $student->full_name }}</td>
                            </tr>
                            @if($student->full_name_en)
                            <tr>
                                <td><strong>{{ __('students.full_name_en') }}:</strong></td>
                                <td>{{ $student->full_name_en }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td><strong>{{ __('students.student_id') }}:</strong></td>
                                <td><code>{{ $student->student_id }}</code></td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('students.email') }}:</strong></td>
                                <td>
                                    @if($student->email)
                                        <a href="mailto:{{ $student->email }}">
                                            <i class="fas fa-envelope me-1"></i>
                                            {{ $student->email }}
                                        </a>
                                    @else
                                        <span class="text-muted">{{ __('common.not_provided') }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('common.department') }}:</strong></td>
                                <td>
                                    @if($student->departmentCategory)
                                        <span class="badge bg-info">{{ $student->departmentCategory->name }}</span>
                                        @if($student->departmentCategory->name_en !== $student->departmentCategory->name)
                                            <br><small class="text-muted">{{ $student->departmentCategory->name_en }}</small>
                                        @endif
                                    @elseif($student->department)
                                        <span class="badge bg-warning">{{ $student->department }}</span>
                                        <br><small class="text-muted">({{ __('common.legacy') }})</small>
                                    @else
                                        <span class="text-muted">{{ __('common.not_assigned') }}</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="150"><strong>{{ __('students.primary_phone') }}:</strong></td>
                                <td>
                                    <a href="tel:{{ $student->phone_primary }}">
                                        <i class="fas fa-phone me-1"></i>
                                        {{ $student->formatted_phone_primary }}
                                    </a>
                                </td>
                            </tr>
                            @if($student->phone_alt)
                            <tr>
                                <td><strong>{{ __('students.alternative_phone') }}:</strong></td>
                                <td>
                                    <a href="tel:{{ $student->phone_alt }}">
                                        <i class="fas fa-phone me-1"></i>
                                        {{ $student->formatted_phone_alt }}
                                    </a>
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <td><strong>{{ __('students.country_code') }}:</strong></td>
                                <td>{{ $student->country_code }}</td>
                            </tr>
                            @if($student->university || $student->major || $student->college)
                            <tr>
                                <td><strong>{{ __('students.university') }} / {{ __('students.major') }} / {{ __('students.college') }}:</strong></td>
                                <td>
                                    {{ $student->university ?? __('common.not_provided') }}
                                    @if($student->major)
                                        <span class="text-muted">•</span> {{ $student->major }}
                                    @endif
                                    @if($student->college)
                                        <span class="text-muted">•</span> {{ $student->college }}
                                    @endif
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <td><strong>{{ __('students.reach_source') }}:</strong></td>
                                <td>{{ $student->reach_source_label }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($student->preferredCourse)
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <i class="fas fa-graduation-cap me-2"></i>
                            <strong>{{ __('students.preferred_course') }}:</strong> 
                            {{ $student->preferredCourse->name_ar ?? $student->preferredCourse->name }}
                            @if($student->preferredCourse->name_en && $student->preferredCourse->name_en !== ($student->preferredCourse->name_ar ?? $student->preferredCourse->name))
                                - {{ $student->preferredCourse->name_en }}
                            @endif
                            <small class="text-muted">({{ $student->preferredCourse->code }})</small>
                        </div>
                    </div>
                </div>
                @endif

                @if($student->notes)
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h6><i class="fas fa-sticky-note me-2"></i>{{ __('students.notes') }}:</h6>
                        <div class="bg-light p-3 rounded">
                            {{ $student->notes }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Timeline Section -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-clock me-2"></i>
                    {{ __('follow_ups.recent_follow_ups') }}
                </h5>
                <a href="{{ route('follow-ups.student', $student) }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-list me-1"></i>
                    {{ __('common.view_all') }}
                </a>
            </div>
            <div class="card-body">
                @if($followUpsStats['total'] > 0)
                    <div class="timeline">
                        @foreach($student->followUps as $followUp)
                        <div class="timeline-item mb-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    @switch($followUp->status)
                                        @case('completed')
                                            <i class="fas fa-check-circle text-success fa-lg"></i>
                                            @break
                                        @case('cancelled')
                                            <i class="fas fa-times-circle text-danger fa-lg"></i>
                                            @break
                                        @case('pending')
                                            <i class="fas fa-clock text-warning fa-lg"></i>
                                            @break
                                        @default
                                            <i class="fas fa-circle text-secondary fa-lg"></i>
                                    @endswitch
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">{{ __('follow_ups.' . $followUp->type, [], 'en') !== 'follow_ups.' . $followUp->type ? __('follow_ups.' . $followUp->type) : ucfirst(str_replace('_', ' ', $followUp->type)) }}</h6>
                                            <p class="text-muted mb-1">{{ __('follow_ups.purposes.' . $followUp->purpose, [], 'en') !== 'follow_ups.purposes.' . $followUp->purpose ? __('follow_ups.purposes.' . $followUp->purpose) : $followUp->purpose }}</p>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>
                                                {{ $followUp->scheduled_date->format('M j, Y g:i A') }}
                                                •
                                                <i class="fas fa-user me-1"></i>
                                                {{ $followUp->user->name ?? __('common.system') }}
                                            </small>
                                        </div>
                                        <div>
                                            <span class="badge bg-{{ $followUp->status === 'completed' ? 'success' : ($followUp->status === 'cancelled' ? 'danger' : 'warning') }}">
                                                {{ __('follow_ups.' . $followUp->status, [], 'en') !== 'follow_ups.' . $followUp->status ? __('follow_ups.' . $followUp->status) : ucfirst($followUp->status) }}
                                            </span>
                                            @if($followUp->priority === 'high')
                                                <span class="badge bg-danger ms-1">{{ __('follow_ups.high_priority') }}</span>
                                            @elseif($followUp->priority === 'medium')
                                                <span class="badge bg-warning ms-1">{{ __('follow_ups.medium_priority') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">{{ __('follow_ups.no_follow_ups_yet') }}</h6>
                        <p class="text-muted small">
                            {{ __('follow_ups.start_tracking_interactions') }}
                        </p>
                        <a href="{{ route('follow-ups.create', ['student_id' => $student->id]) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>
                            {{ __('follow_ups.add_first_follow_up') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Enrollments Section -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-book me-2"></i>
                    {{ __('students.course_enrollments') }}
                </h5>
                <a href="{{ route('enrollments.create', ['student_id' => $student->id]) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    {{ __('students.add_enrollment') }}
                </a>
            </div>
            <div class="card-body">
                @if($student->activeEnrollments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('enrollments.class') }}</th>
                                    <th>{{ __('enrollments.course') }}</th>
                                    <th>{{ __('enrollments.enrollment_date') }}</th>
                                    <th>{{ __('enrollments.total_amount') }}</th>
                                    <th>{{ __('enrollments.payment_status') }}</th>
                                    <th>{{ __('common.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($student->activeEnrollments as $enrollment)
                                <tr>
                                    <td>
                                        <strong>{{ $enrollment->courseClass->class_name }}</strong>
                                        <br><small class="text-muted">{{ $enrollment->courseClass->class_code }}</small>
                                    </td>
                                    <td>{{ $enrollment->courseClass->course->name }}</td>
                                    <td>{{ $enrollment->enrollment_date->format('M d, Y') }}</td>
                                    <td>
                                        {{ number_format($enrollment->total_amount, 2) }} {{ __('common.currency') }}
                                        @if($enrollment->due_amount > 0)
                                            <br><small class="text-danger">{{ __('enrollments.due') }}: {{ number_format($enrollment->due_amount, 2) }} {{ __('common.currency') }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @switch($enrollment->payment_status)
                                            @case('completed')
                                                <span class="badge bg-success">{{ __('enrollments.completed') }}</span>
                                                @break
                                            @case('partial')
                                                <span class="badge bg-warning">{{ __('enrollments.partial') }}</span>
                                                @break
                                            @case('not_paid')
                                                <span class="badge bg-danger">{{ __('enrollments.not_paid') }}</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('enrollments.show', $enrollment) }}" 
                                               class="btn btn-outline-primary" 
                                               title="{{ __('common.view') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($enrollment->payment_status !== 'completed' && $enrollment->due_amount > 0)
                                                <button class="btn btn-outline-success enrollment-payment-btn" 
                                                        title="{{ __('enrollments.add_payment') }}"
                                                        data-enrollment-id="{{ $enrollment->id }}"
                                                        data-enrollment-class="{{ $enrollment->courseClass->class_name }}"
                                                        data-enrollment-course="{{ $enrollment->courseClass->course->name }}"
                                                        data-due-amount="{{ $enrollment->due_amount }}"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#quickPaymentModal">
                                                    <i class="fas fa-dollar-sign"></i>
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
                        <i class="fas fa-book fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">{{ __('students.no_enrollments_yet') }}</h6>
                        <p class="text-muted small">
                            {{ __('students.enrollments_section_description') }}
                        </p>
                        <a href="{{ route('enrollments.create', ['student_id' => $student->id]) }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>
                            {{ __('students.add_enrollment') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-md-4">
        <!-- Follow-ups Summary -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-comments me-2"></i>
                    {{ __('follow_ups.follow_ups_summary') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="text-center">
                            <div class="h3 mb-1 text-primary">{{ $followUpsStats['total'] }}</div>
                            <small class="text-muted">{{ __('common.total') }}</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <div class="h3 mb-1 text-warning">{{ $followUpsStats['pending'] }}</div>
                            <small class="text-muted">{{ __('follow_ups.pending') }}</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <div class="h3 mb-1 text-success">{{ $followUpsStats['completed'] }}</div>
                            <small class="text-muted">{{ __('follow_ups.completed') }}</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <div class="h3 mb-1 text-danger">{{ $followUpsStats['overdue'] }}</div>
                            <small class="text-muted">{{ __('follow_ups.overdue') }}</small>
                        </div>
                    </div>
                </div>
                @if($followUpsStats['total'] > 0)
                    <hr>
                    <div class="d-grid">
                        <a href="{{ route('follow-ups.student', $student) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-list me-1"></i>
                            {{ __('follow_ups.view_all_follow_ups') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bolt me-2"></i>
                    {{ __('students.quick_actions') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="tel:{{ $student->phone_primary }}" class="btn btn-outline-success">
                        <i class="fas fa-phone me-2"></i>
                        {{ __('students.call_primary_phone') }}
                    </a>
                    @if($student->phone_alt)
                    <a href="tel:{{ $student->phone_alt }}" class="btn btn-outline-success">
                        <i class="fas fa-phone me-2"></i>
                        {{ __('students.call_alt_phone') }}
                    </a>
                    @endif
                    @if($student->email)
                    <a href="mailto:{{ $student->email }}" class="btn btn-outline-primary">
                        <i class="fas fa-envelope me-2"></i>
                        {{ __('students.send_email') }}
                    </a>
                    @endif
                    <a href="{{ route('follow-ups.create', ['student_id' => $student->id]) }}" class="btn btn-outline-info">
                        <i class="fas fa-comment me-2"></i>
                        {{ __('follow_ups.add_follow_up') }}
                    </a>
                    <a href="{{ route('enrollments.create', ['student_id' => $student->id]) }}" class="btn btn-outline-success">
                        <i class="fas fa-user-plus me-2"></i>
                        {{ __('students.enroll_in_course') }}
                    </a>
                    @if($student->activeEnrollments->where('due_amount', '>', 0)->count() > 0)
                    <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                        <i class="fas fa-dollar-sign me-2"></i>
                        {{ __('students.add_payment') }}
                    </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Student Stats -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    {{ __('students.student_stats') }}
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td><strong>{{ __('students.joined') }}:</strong></td>
                        <td>{{ $student->created_at->format('M d, Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>{{ __('students.last_updated') }}:</strong></td>
                        <td>{{ $student->updated_at->format('M d, Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>{{ __('common.status') }}:</strong></td>
                        <td>
                            <span class="badge bg-success">Active</span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>{{ __('follow_ups.follow_ups') }}:</strong></td>
                        <td>
                            <span class="badge bg-{{ $followUpsStats['total'] > 0 ? 'info' : 'secondary' }}">
                                {{ $followUpsStats['total'] }}
                            </span>
                            @if($followUpsStats['total'] > 0)
                                <small class="text-muted ms-2">
                                    {{ $followUpsStats['pending'] }} {{ __('follow_ups.pending') }}
                                    @if($followUpsStats['overdue'] > 0)
                                        • <span class="text-danger">{{ $followUpsStats['overdue'] }} {{ __('follow_ups.overdue') }}</span>
                                    @endif
                                </small>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><strong>{{ __('students.enrollments') }}:</strong></td>
                        <td>
                            <span class="badge bg-{{ $student->activeEnrollments->count() > 0 ? 'info' : 'secondary' }}">
                                {{ $student->activeEnrollments->count() }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('students.confirm_deletion') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>{{ __('students.are_you_sure_delete') }} <strong>{{ $student->full_name }}</strong>?</p>
                <p class="text-danger small">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    {{ __('students.deletion_warning') }}
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                <form method="POST" action="{{ route('students.destroy', $student) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>
                        {{ __('students.delete_student') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Payment Modal -->
@if($student->activeEnrollments->where('due_amount', '>', 0)->count() > 0)
<div class="modal fade" id="addPaymentModal" tabindex="-1" aria-labelledby="addPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPaymentModalLabel">
                    <i class="fas fa-dollar-sign me-2"></i>
                    {{ __('students.payment_management') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('students.payments.store', $student) }}" id="addPaymentForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="enrollment_id" class="form-label">
                            {{ __('students.select_enrollment') }} <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="enrollment_id" name="enrollment_id" required>
                            <option value="">{{ __('students.select_enrollment_for_payment') }}</option>
                            @foreach($student->activeEnrollments->where('due_amount', '>', 0) as $enrollment)
                                <option value="{{ $enrollment->id }}" data-due-amount="{{ $enrollment->due_amount }}">
                                    {{ $enrollment->courseClass->class_name }} - {{ $enrollment->courseClass->course->name }}
                                    ({{ __('students.due_amount') }}: {{ number_format($enrollment->due_amount, 2) }} {{ __('common.currency') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_amount" class="form-label">
                            {{ __('students.payment_amount') }} ({{ __('common.currency') }}) <span class="text-danger">*</span>
                        </label>
                        <input type="number" class="form-control" id="payment_amount" name="amount" 
                               min="0.01" step="0.01" required>
                        <div class="form-text" id="dueAmountHelper" style="display: none;">
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
                        <label for="payment_notes" class="form-label">{{ __('students.payment_notes') }}</label>
                        <textarea class="form-control" id="payment_notes" name="notes" rows="3" 
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
@endif

<!-- Quick Payment Modal (for enrollment-specific payments) -->
<div class="modal fade" id="quickPaymentModal" tabindex="-1" aria-labelledby="quickPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quickPaymentModalLabel">
                    <i class="fas fa-dollar-sign me-2"></i>
                    {{ __('students.add_payment') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('students.payments.store', $student) }}" id="quickPaymentForm">
                @csrf
                <input type="hidden" id="quick_enrollment_id" name="enrollment_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('enrollments.class') }} / {{ __('enrollments.course') }}</label>
                        <input type="text" class="form-control" id="quick_enrollment_info" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="quick_payment_amount" class="form-label">
                            {{ __('students.payment_amount') }} ({{ __('common.currency') }}) <span class="text-danger">*</span>
                        </label>
                        <input type="number" class="form-control" id="quick_payment_amount" name="amount" 
                               min="0.01" step="0.01" required>
                        <div class="form-text" id="quick_due_amount_helper">
                            {{ __('payments.maximum_amount') }}: <span id="quick_max_amount"></span> {{ __('common.currency') }}
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="quick_payment_method" class="form-label">
                                    {{ __('students.payment_method') }} <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="quick_payment_method" name="payment_method" required>
                                    <option value="cash">{{ __('payments.cash') }}</option>
                                    <option value="bank_transfer">{{ __('payments.bank_transfer') }}</option>
                                    <option value="credit_card">{{ __('payments.credit_card') }}</option>
                                    <option value="check">{{ __('payments.check') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="quick_payment_date" class="form-label">
                                    {{ __('students.payment_date') }} <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control" id="quick_payment_date" name="payment_date" 
                                       value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="quick_notes" class="form-label">{{ __('students.notes') }}</label>
                        <textarea class="form-control" id="quick_notes" name="notes" rows="3" 
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

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const enrollmentSelect = document.getElementById('enrollment_id');
    const amountInput = document.getElementById('payment_amount');
    const dueAmountHelper = document.getElementById('dueAmountHelper');
    const maxAmountSpan = document.getElementById('maxAmount');
    const enrollmentPaymentButtons = document.querySelectorAll('.enrollment-payment-btn');
    
    // Handle enrollment dropdown selection (existing functionality)
    if (enrollmentSelect) {
        enrollmentSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const dueAmount = selectedOption.getAttribute('data-due-amount');
            
            if (dueAmount && dueAmount > 0) {
                setPaymentAmount(dueAmount);
            } else {
                resetPaymentAmount();
            }
        });
    }
    
    // Handle quick payment button clicks from enrollment table
    enrollmentPaymentButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const enrollmentId = this.getAttribute('data-enrollment-id');
            const enrollmentClass = this.getAttribute('data-enrollment-class');
            const enrollmentCourse = this.getAttribute('data-enrollment-course');
            const dueAmount = this.getAttribute('data-due-amount');
            
            // Set data in quick payment modal
            document.getElementById('quick_enrollment_id').value = enrollmentId;
            document.getElementById('quick_enrollment_info').value = `${enrollmentClass} - ${enrollmentCourse}`;
            
            // Set payment amount details
            const quickAmountInput = document.getElementById('quick_payment_amount');
            const quickMaxAmountSpan = document.getElementById('quick_max_amount');
            
            if (dueAmount && dueAmount > 0) {
                quickAmountInput.max = dueAmount;
                quickMaxAmountSpan.textContent = parseFloat(dueAmount).toFixed(2);
                quickAmountInput.value = parseFloat(dueAmount).toFixed(2);
            }
        });
    });
    
    // Helper function to set payment amount (for existing modal)
    function setPaymentAmount(dueAmount) {
        if (amountInput && maxAmountSpan && dueAmountHelper) {
            amountInput.max = dueAmount;
            maxAmountSpan.textContent = parseFloat(dueAmount).toFixed(2);
            dueAmountHelper.style.display = 'block';
            
            // Set the amount to full due amount by default
            amountInput.value = parseFloat(dueAmount).toFixed(2);
        }
    }
    
    // Helper function to reset payment amount (for existing modal)
    function resetPaymentAmount() {
        if (amountInput && dueAmountHelper) {
            dueAmountHelper.style.display = 'none';
            amountInput.removeAttribute('max');
            amountInput.value = '';
        }
    }
    
    // Validate quick payment amount
    const quickPaymentAmount = document.getElementById('quick_payment_amount');
    if (quickPaymentAmount) {
        quickPaymentAmount.addEventListener('input', function() {
            const amount = parseFloat(this.value);
            const maxAmount = parseFloat(this.max);
            
            if (amount > maxAmount) {
                this.setCustomValidity('{{ __("payments.amount_exceeds_due_amount_js") }}');
            } else {
                this.setCustomValidity('');
            }
        });
    }
});
</script>
@endpush