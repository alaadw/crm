@extends('layouts.app')

@section('title', __('follow_ups.add_follow_up') . ' - CRM Academy')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">
                <i class="fas fa-comment-alt me-2"></i>
                {{ __('follow_ups.add_follow_up') }}
            </h1>
            <a href="{{ route('students.show', $student) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                {{ __('follow_ups.back_to_student') }}
            </a>
        </div>

        <!-- Student Info Card -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="card-title mb-1">{{ $student->full_name }}</h5>
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

        <!-- Follow-up Form -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-plus me-2"></i>
                    {{ __('follow_ups.new_follow_up_details') }}
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('follow-ups.store') }}">
                    @csrf
                    <input type="hidden" name="student_id" value="{{ $student->id }}">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="scheduled_date" class="form-label">
                                {{ __('follow_ups.scheduled_date') }} <span class="text-danger">*</span>
                            </label>
                            <input type="datetime-local" 
                                   class="form-control @error('scheduled_date') is-invalid @enderror" 
                                   id="scheduled_date" 
                                   name="scheduled_date" 
                                   value="{{ old('scheduled_date', now()->addHour()->format('Y-m-d\TH:i')) }}" 
                                   required>
                            @error('scheduled_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="priority" class="form-label">
                                {{ __('common.priority') }} <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('priority') is-invalid @enderror" 
                                    id="priority" name="priority" required>
                                <option value="">{{ __('common.select_option') }}</option>
                                @foreach($priorities as $value => $label)
                                    <option value="{{ $value }}" {{ old('priority') === $value ? 'selected' : '' }}>
                                        {{ __('common.' . $value) }}
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
                                <option value="">{{ __('common.select_option') }}</option>
                                <option value="phone" {{ old('contact_method') === 'phone' ? 'selected' : '' }}>{{ __('common.phone_call') }}</option>
                                <option value="whatsapp" {{ old('contact_method') === 'whatsapp' ? 'selected' : '' }}>{{ __('common.whatsapp') }}</option>
                                <option value="email" {{ old('contact_method') === 'email' ? 'selected' : '' }}>{{ __('common.email') }}</option>
                                <option value="in_person" {{ old('contact_method') === 'in_person' ? 'selected' : '' }}>{{ __('common.in_person') }}</option>
                            </select>
                            @error('contact_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="type" class="form-label">
                                {{ __('follow_ups.follow_up_type') }} <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('type') is-invalid @enderror" 
                                    id="type" name="type" required>
                                <option value="">{{ __('follow_ups.select_type') }}</option>
                                <option value="initial_contact" {{ old('type') === 'initial_contact' ? 'selected' : '' }}>{{ __('follow_ups.initial_contact') }}</option>
                                <option value="course_inquiry" {{ old('type') === 'course_inquiry' ? 'selected' : '' }}>{{ __('follow_ups.course_inquiry') }}</option>
                                <option value="payment_reminder" {{ old('type') === 'payment_reminder' ? 'selected' : '' }}>{{ __('follow_ups.payment_reminder') }}</option>
                                <option value="enrollment_follow_up" {{ old('type') === 'enrollment_follow_up' ? 'selected' : '' }}>{{ __('follow_ups.enrollment_follow_up') }}</option>
                                <option value="customer_service" {{ old('type') === 'customer_service' ? 'selected' : '' }}>{{ __('follow_ups.customer_service') }}</option>
                                <option value="other" {{ old('type') === 'other' ? 'selected' : '' }}>{{ __('follow_ups.other') }}</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="course_id" class="form-label">
                                {{ __('follow_ups.related_course') }}
                            </label>
                            <select class="form-select @error('course_id') is-invalid @enderror" 
                                    id="course_id" name="course_id">
                                <option value="">{{ __('follow_ups.select_course') }}</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->name_ar }} - {{ $course->name_en }}
                                    </option>
                                @endforeach
                            </select>
                            @error('course_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="purpose" class="form-label">
                            {{ __('follow_ups.purpose_agenda') }} <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('purpose') is-invalid @enderror" 
                                  id="purpose" 
                                  name="purpose" 
                                  rows="3" 
                                  placeholder="{{ __('follow_ups.purpose_placeholder') }}"
                                  required>{{ old('purpose') }}</textarea>
                        @error('purpose')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="notes" class="form-label">{{ __('follow_ups.additional_notes') }}</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" 
                                  name="notes" 
                                  rows="2" 
                                  placeholder="{{ __('follow_ups.notes_placeholder') }}">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('students.show', $student) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>
                            {{ __('common.cancel') }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            {{ __('follow_ups.save_follow_up') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection