@extends('layouts.app')

@section('title', __('enrollments.enroll_student') . ' - CRM Academy')

@section('content')
<div class="row justify-c                        <div class="col-md-6">
                            <label for="paid_amount" class="form-label">
                                {{ __('enrollments.paid_amount') }}
                            </label>
                            <input type="number" 
                                   class="form-control @error('paid_amount') is-invalid @enderror" 
                                   id="paid_amount" 
                                   name="paid_amount" 
                                   value="{{ old('paid_amount', 0) }}" 
                                   min="0" 
                                   step="0.01">
                            @error('paid_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">{{ __('enrollments.amount_paid_at_enrollment') }}</div>
                        </div>
    <div class="col-md-8">
        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="d-flex align-items-center">
                <a href="{{ route('students.show', $student) }}" class="btn btn-outline-secondary me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-user-plus me-2"></i>
                        {{ __('enrollments.enroll_student') }}
                    </h1>
                    <small class="text-muted">{{ $student->full_name }} ({{ $student->student_id }})</small>
                </div>
            </div>
        </div>

        <!-- Student Info Card -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="card-title mb-1">{{ $student->full_name }}</h5>
                        @if($student->full_name_en)
                            <p class="text-muted mb-1">{{ $student->full_name_en }}</p>
                        @endif
                        <p class="text-muted mb-0">
                            <i class="fas fa-phone me-1"></i>
                            {{ $student->formatted_phone_primary }}
                        </p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <span class="badge bg-info">{{ $student->student_id }}</span>
                        @if($student->departmentCategory)
                            <span class="badge bg-secondary">{{ $student->departmentCategory->name }}</span>
                        @elseif($student->department)
                            <span class="badge bg-warning">{{ $student->department }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Enrollment Form -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-graduation-cap me-2"></i>
                    {{ __('enrollments.enrollment_details') }}
                </h5>
            </div>
            <div class="card-body">
                <!-- Display general errors -->
                @if($errors->any())
                    <div class="alert alert-danger">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>{{ __('common.validation_errors') }}</h6>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('enrollments.store') }}">
                    @csrf
                    <input type="hidden" name="student_id" value="{{ $student->id }}">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="department" class="form-label">
                                {{ __('common.department') }} <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('department') is-invalid @enderror" 
                                    id="department" name="department" required>
                                <option value="">{{ __('common.select_department') }}</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}">
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="course_class_id" class="form-label">
                                {{ __('enrollments.select_class') }} <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('course_class_id') is-invalid @enderror" 
                                    id="course_class_id" name="course_class_id" required>
                                <option value="">{{ __('enrollments.first_select_department') }}</option>
                            </select>
                            @error('course_class_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="enrollment_date" class="form-label">
                                {{ __('enrollments.enrollment_date') }} <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   class="form-control @error('enrollment_date') is-invalid @enderror" 
                                   id="enrollment_date" 
                                   name="enrollment_date" 
                                   value="{{ old('enrollment_date', now()->format('Y-m-d')) }}" 
                                   required>
                            @error('enrollment_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="total_amount" class="form-label">
                                {{ __('enrollments.total_amount') }} <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   class="form-control @error('total_amount') is-invalid @enderror" 
                                   id="total_amount" 
                                   name="total_amount" 
                                   value="{{ old('total_amount') }}" 
                                   min="0" 
                                   step="0.01"
                                   required>
                            @error('total_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">{{ __('enrollments.amount_in_selected_currency') }}</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="paid_amount" class="form-label">
                                {{ __('enrollments.paid_amount') }} ({{ __('common.currency') }})
                            </label>
                            <input type="number" 
                                   class="form-control @error('paid_amount') is-invalid @enderror" 
                                   id="paid_amount" 
                                   name="paid_amount" 
                                   value="{{ old('paid_amount', 0) }}" 
                                   min="0" 
                                   step="0.01">
                            @error('paid_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">{{ __('enrollments.amount_paid_at_enrollment') }}</div>
                        </div>
                        <div class="col-md-3">
                            <label for="currency_code" class="form-label">
                                {{ __('currencies.currency') }}
                            </label>
                            <select class="form-select @error('currency_code') is-invalid @enderror" 
                                    id="currency_code" name="currency_code">
                                @foreach($currencies as $currency)
                                    <option value="{{ $currency->code }}" 
                                            data-rate="{{ $currency->exchange_rate_to_jod }}"
                                            {{ old('currency_code', 'JOD') === $currency->code ? 'selected' : '' }}>
                                        {{ $currency->display_name }} ({{ $currency->symbol }})
                                    </option>
                                @endforeach
                            </select>
                            @error('currency_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="payment_method" class="form-label">
                                {{ __('enrollments.payment_method') }}
                            </label>
                            <select class="form-select @error('payment_method') is-invalid @enderror" 
                                    id="payment_method" name="payment_method">
                                <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>{{ __('payments.cash') }}</option>
                                <option value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'selected' : '' }}>{{ __('payments.bank_transfer') }}</option>
                                <option value="credit_card" {{ old('payment_method') === 'credit_card' ? 'selected' : '' }}>{{ __('payments.credit_card') }}</option>
                                <option value="check" {{ old('payment_method') === 'check' ? 'selected' : '' }}>{{ __('payments.check') }}</option>
                                <option value="zaincash" {{ old('payment_method') === 'zaincash' ? 'selected' : '' }}>{{ __('payments.zaincash') }}</option>
                                <option value="other" {{ old('payment_method') === 'other' ? 'selected' : '' }}>{{ __('payments.other') }}</option>
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-4" id="jod_equivalent_section" style="display: none;">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <span id="jod_equivalent_text"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label for="notes" class="form-label">{{ __('enrollments.notes') }}</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" 
                                      name="notes" 
                                      rows="3" 
                                      placeholder="{{ __('enrollments.enrollment_notes_placeholder') }}">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('students.show', $student) }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i>
                                    {{ __('common.cancel') }}
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>
                                    {{ __('enrollments.enroll_student') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const courseClassesData = @json($courseClassesData);

    // Department change handler
    $('#department').change(function() {
        const selectedDepartment = $(this).val();
        const courseClassSelect = $('#course_class_id');
        const totalAmountInput = $('#total_amount');
        
        // Clear previous options
        courseClassSelect.html('<option value="">{{ __("enrollments.select_class") }}</option>');
        totalAmountInput.val('');
        
        if (selectedDepartment && courseClassesData[selectedDepartment]) {
            const classes = courseClassesData[selectedDepartment];
            
            classes.forEach(function(classData) {
                const option = $('<option></option>')
                    .attr('value', classData.id)
                    .attr('data-price', classData.price)
                    .text(`${classData.name} - ${classData.course_name} (${classData.start_date})`);
                courseClassSelect.append(option);
            });
        }
    });

    // Class selection handler - auto-fill price
    $('#course_class_id').change(function() {
        const selectedOption = $(this).find('option:selected');
        const price = selectedOption.data('price');
        
        if (price) {
            $('#total_amount').val(price);
        }
    });

    // Calculate due amount when paid amount changes
    $('#paid_amount, #total_amount').on('input', function() {
        const totalAmount = parseFloat($('#total_amount').val()) || 0;
        const paidAmount = parseFloat($('#paid_amount').val()) || 0;
        const dueAmount = totalAmount - paidAmount;
        
        updateJODEquivalent();
    });

    // Currency change handler - show JOD equivalent
    $('#currency_code, #paid_amount').on('change input', function() {
        updateJODEquivalent();
    });

    function updateJODEquivalent() {
        const paidAmount = parseFloat($('#paid_amount').val()) || 0;
        const currencyCode = $('#currency_code').val();
        const exchangeRate = parseFloat($('#currency_code option:selected').data('rate')) || 1;
        
        if (paidAmount > 0 && currencyCode && currencyCode !== 'JOD') {
            const amountInJOD = paidAmount * exchangeRate;
            const currencyName = $('#currency_code option:selected').text();
            
            $('#jod_equivalent_text').html(
                `<strong>${paidAmount.toFixed(2)} ${currencyCode}</strong> = <strong>${amountInJOD.toFixed(2)} JD</strong> {{ __('currencies.equivalent_in_jod') }}`
            );
            $('#jod_equivalent_section').slideDown();
        } else {
            $('#jod_equivalent_section').slideUp();
        }
    }

    // Initialize on page load
    updateJODEquivalent();
});
</script>
@endpush