@extends('layouts.app')

@section('title', __('follow_ups.edit_follow_up') . ' - CRM Academy')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">
                <i class="fas fa-edit me-2"></i>
                {{ __('follow_ups.edit_follow_up') }}
            </h1>
            <div>
                <a href="{{ route('follow-ups.student', $followUp->student) }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-list me-1"></i>
                    {{ __('follow_ups.back_to_follow_ups') }}
                </a>
                <a href="{{ route('students.show', $followUp->student) }}" class="btn btn-outline-primary">
                    <i class="fas fa-user me-1"></i>
                    {{ __('follow_ups.back_to_student') }}
                </a>
            </div>
        </div>

        <!-- Student Info Card -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="card-title mb-1">{{ $followUp->student->full_name }}</h5>
                        <p class="text-muted mb-0">
                            <i class="fas fa-phone me-1"></i>
                            {{ $followUp->student->formatted_phone_primary }}
                        </p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <span class="badge bg-info">{{ $followUp->student->student_id }}</span>
                        @if($followUp->student->departmentCategory)
                            <span class="badge bg-secondary">{{ $followUp->student->departmentCategory->name }}</span>
                        @elseif($followUp->student->department)
                            <span class="badge bg-warning">{{ $followUp->student->department }}</span>
                        @endif
                        <div class="mt-2">
                            <small class="text-muted">
                                {{ __('common.created') }}: {{ $followUp->created_at->format('M j, Y g:i A') }}
                                {{ __('common.by') }} {{ $followUp->user->name ?? 'System' }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Follow-up Edit Form -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-edit me-2"></i>
                    {{ __('follow_ups.update_follow_up_details') }}
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('follow-ups.update', $followUp) }}">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="scheduled_date" class="form-label">
                                {{ __('follow_ups.scheduled_date') }} <span class="text-danger">*</span>
                            </label>
                            <input type="datetime-local" 
                                   class="form-control @error('scheduled_date') is-invalid @enderror" 
                                   id="scheduled_date" 
                                   name="scheduled_date" 
                                   value="{{ old('scheduled_date', $followUp->scheduled_date->format('Y-m-d\TH:i')) }}" 
                                   required>
                            @error('scheduled_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="priority" class="form-label">
                                {{ __('follow_ups.priority') }} <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('priority') is-invalid @enderror" 
                                    id="priority" name="priority" required>
                                <option value="">{{ __('follow_ups.select_priority') }}</option>
                                @foreach($priorities as $value => $label)
                                    <option value="{{ $value }}" {{ old('priority', $followUp->priority) === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="contact_method" class="form-label">
                                {{ __('follow_ups.contact_method') }} <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('contact_method') is-invalid @enderror" 
                                    id="contact_method" name="contact_method" required>
                                <option value="">{{ __('follow_ups.select_method') }}</option>
                                <option value="phone" {{ old('contact_method', $followUp->contact_method) === 'phone' ? 'selected' : '' }}>{{ __('follow_ups.phone_call') }}</option>
                                <option value="whatsapp" {{ old('contact_method', $followUp->contact_method) === 'whatsapp' ? 'selected' : '' }}>{{ __('follow_ups.whatsapp') }}</option>
                                <option value="email" {{ old('contact_method', $followUp->contact_method) === 'email' ? 'selected' : '' }}>{{ __('follow_ups.email') }}</option>
                                <option value="in_person" {{ old('contact_method', $followUp->contact_method) === 'in_person' ? 'selected' : '' }}>{{ __('follow_ups.in_person') }}</option>
                            </select>
                            @error('contact_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="type" class="form-label">
                                {{ __('follow_ups.type') }} <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('type') is-invalid @enderror" 
                                    id="type" name="type" required>
                                <option value="">{{ __('follow_ups.select_type') }}</option>
                                <option value="initial_contact" {{ old('type', $followUp->type) === 'initial_contact' ? 'selected' : '' }}>{{ __('follow_ups.initial_contact') }}</option>
                                <option value="course_inquiry" {{ old('type', $followUp->type) === 'course_inquiry' ? 'selected' : '' }}>{{ __('follow_ups.course_inquiry') }}</option>
                                <option value="payment_reminder" {{ old('type', $followUp->type) === 'payment_reminder' ? 'selected' : '' }}>{{ __('follow_ups.payment_reminder') }}</option>
                                <option value="enrollment_follow_up" {{ old('type', $followUp->type) === 'enrollment_follow_up' ? 'selected' : '' }}>{{ __('follow_ups.enrollment_follow_up') }}</option>
                                <option value="customer_service" {{ old('type', $followUp->type) === 'customer_service' ? 'selected' : '' }}>{{ __('follow_ups.customer_service') }}</option>
                                <option value="other" {{ old('type', $followUp->type) === 'other' ? 'selected' : '' }}>{{ __('follow_ups.other') }}</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="department" class="form-label">
                                {{ __('common.department') }}
                            </label>
                            <select class="form-select" id="department" name="department">
                                <option value="">{{ __('common.select_department') }}</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ (old('department', $selectedDepartmentId) == $dept->id) ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="course_id" class="form-label">
                                {{ __('follow_ups.related_course') }}
                            </label>
                            <select class="form-select @error('course_id') is-invalid @enderror" id="course_id" name="course_id">
                                <option value="">{{ __('follow_ups.select_course') }}</option>
                            </select>
                            @error('course_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="status" class="form-label">
                                {{ __('follow_ups.status') }} <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                @foreach($statuses as $value => $label)
                                    @php
                                        $statusKey = $value === 'expected' ? 'expected_to_register' : $value;
                                        $statusLabel = __("follow_ups.$statusKey");
                                    @endphp
                                    <option value="{{ $value }}" {{ old('status', $followUp->status_key) === $value ? 'selected' : '' }}>
                                        {{ $statusLabel }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="outcome" class="form-label">{{ __('follow_ups.outcome') }}</label>
                            <select class="form-select @error('outcome') is-invalid @enderror" 
                                    id="outcome" name="outcome">
                                <option value="">{{ __('follow_ups.select_outcome') }}</option>
                                @php
                                    $outcomeMap = [
                                        'Interested' => 'student_interested',
                                        'No Answer' => 'no_answer',
                                        'Wrong Number' => 'wrong_number',
                                        'Not Interested' => 'student_not_interested',
                                        'Callback Requested' => 'busy_callback',
                                    ];
                                @endphp
                                @foreach($outcomes as $outcome)
                                    @php
                                        $key = $outcomeMap[$outcome] ?? null;
                                        $label = $key ? __("follow_ups.$key") : $outcome;
                                    @endphp
                                    <option value="{{ $outcome }}" {{ old('outcome', $followUp->outcome) === $outcome ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('outcome')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="purpose" class="form-label">
                            {{ __('follow_ups.purpose') }} <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('purpose') is-invalid @enderror" 
                                  id="purpose" 
                                  name="purpose" 
                                  rows="3" 
                                  placeholder="{{ __('follow_ups.purpose_placeholder') }}"
                                  required>{{ old('purpose', $followUp->purpose) }}</textarea>
                        @error('purpose')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">{{ __('follow_ups.additional_notes') }}</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" 
                                  name="notes" 
                                  rows="2" 
                                  placeholder="{{ __('follow_ups.notes_placeholder') }}">{{ old('notes', $followUp->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="action_note" class="form-label">{{ __('follow_ups.action_notes') }}</label>
                        <textarea class="form-control @error('action_note') is-invalid @enderror" 
                                  id="action_note" 
                                  name="action_note" 
                                  rows="2" 
                                  placeholder="{{ __('follow_ups.action_note_placeholder') }}">{{ old('action_note', $followUp->action_note) }}</textarea>
                        @error('action_note')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Cancellation Fields (shown conditionally) -->
                    <div id="cancellation-fields" style="display: none;">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="cancellation_reason" class="form-label">{{ __('follow_ups.cancellation_reason') }}</label>
                                <select class="form-select @error('cancellation_reason') is-invalid @enderror" 
                                        id="cancellation_reason" name="cancellation_reason">
                                    <option value="">{{ __('follow_ups.select_reason') }}</option>
                                    @foreach($cancellationReasons as $reason)
                                        <option value="{{ $reason }}" {{ old('cancellation_reason', $followUp->cancellation_reason) === $reason ? 'selected' : '' }}>
                                            {{ $reason }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('cancellation_reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="cancellation_details" class="form-label">{{ __('follow_ups.cancellation_details') }}</label>
                                <textarea class="form-control @error('cancellation_details') is-invalid @enderror" 
                                          id="cancellation_details" 
                                          name="cancellation_details" 
                                          rows="2" 
                                          placeholder="{{ __('follow_ups.cancellation_details_placeholder') }}">{{ old('cancellation_details', $followUp->cancellation_details) }}</textarea>
                                @error('cancellation_details')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('follow-ups.student', $followUp->student) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>
                            {{ __('common.cancel') }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            {{ __('follow_ups.update_follow_up') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // CSRF token for session-authenticated API calls
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function loadCoursesByDepartment(deptId, selectedCourseId = null) {
        const courseSelect = $('#course_id');
    courseSelect.html('<option value="">{{ __('follow_ups.select_course') }}</option>');
        if (!deptId) return;

        fetch(`/api/courses-by-category?category_id=${deptId}`, {
            headers: { 'X-CSRF-TOKEN': csrfToken }
        })
        .then(res => res.json())
        .then(list => {
            list.forEach(c => {
                const opt = $('<option></option>')
                    .attr('value', c.id)
                    .text(c.name);
                if (selectedCourseId && String(selectedCourseId) === String(c.id)) {
                    opt.attr('selected', 'selected');
                }
                courseSelect.append(opt);
            });
        })
        .catch(() => {});
    }

    // When department changes, load its courses
    $('#department').on('change', function() {
        loadCoursesByDepartment($(this).val());
    });

    // On initial load, preselect department and load its courses
    const initialDept = $('#department').val();
    const selectedCourseId = '{{ old('course_id', $followUp->course_id) }}';
    if (initialDept) {
        loadCoursesByDepartment(initialDept, selectedCourseId);
    }
    // Show/hide cancellation fields based on status
    function toggleCancellationFields() {
        const status = $('#status').val();
        if (status === 'cancelled') {
            $('#cancellation-fields').show();
            $('#cancellation_reason').prop('required', true);
        } else {
            $('#cancellation-fields').hide();
            $('#cancellation_reason').prop('required', false);
            $('#cancellation_reason').val('');
            $('#cancellation_details').val('');
        }
    }

    // Initial check
    toggleCancellationFields();

    // Listen for status changes
    $('#status').change(function() {
        toggleCancellationFields();
    });
});
</script>
@endpush
@endsection