@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>{{ __('classes.add_new_class') }}</h2>
                <a href="{{ route('classes.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-right me-1"></i>
                    {{ __('classes.back_to_list') }}
                </a>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('classes.class_information') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('classes.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <!-- Class Name -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="class_name" class="form-label">
                                        {{ __('classes.class_name') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('class_name') is-invalid @enderror" 
                                           id="class_name" 
                                           name="class_name" 
                                           value="{{ old('class_name') }}" 
                                           required>
                                    @error('class_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Class Code -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="class_code" class="form-label">
                                        {{ __('classes.class_code') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('class_code') is-invalid @enderror" 
                                           id="class_code" 
                                           name="class_code" 
                                           value="{{ old('class_code') }}" 
                                           required>
                                    @error('class_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Department -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">
                                        {{ __('common.department') }} <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('category_id') is-invalid @enderror" 
                                            id="category_id" 
                                            name="category_id" 
                                            required>
                                        <option value="">{{ __('common.select_department') }}</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" 
                                                    {{ old('category_id') == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Course -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="course_id" class="form-label">
                                        {{ __('common.courses') }} <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('course_id') is-invalid @enderror" 
                                            id="course_id" 
                                            name="course_id" 
                                            required
                                            disabled>
                                        <option value="">{{ __('classes.first_select_department') }}</option>
                                    </select>
                                    @error('course_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Start Date -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">
                                        {{ __('classes.start_date') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" 
                                           class="form-control @error('start_date') is-invalid @enderror" 
                                           id="start_date" 
                                           name="start_date" 
                                           value="{{ old('start_date') }}" 
                                           required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Moodle Course -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="moodle_course_id" class="form-label">
                                        {{ __('classes.moodle_course') }}
                                    </label>
                                    <select class="form-select @error('moodle_course_id') is-invalid @enderror" 
                                            id="moodle_course_id" 
                                            name="moodle_course_id">
                                        <option value="">{{ __('classes.select_moodle_course') }}</option>
                                    </select>
                                    @error('moodle_course_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">{{ __('classes.moodle_course_help') }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- End Date -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">
                                        {{ __('classes.end_date') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" 
                                           class="form-control @error('end_date') is-invalid @enderror" 
                                           id="end_date" 
                                           name="end_date" 
                                           value="{{ old('end_date') }}" 
                                           required>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Class Fee -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="class_fee" class="form-label">
                                        {{ __('classes.class_fee') }} ({{ __('common.currency') }}) <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" 
                                           class="form-control @error('class_fee') is-invalid @enderror" 
                                           id="class_fee" 
                                           name="class_fee" 
                                           value="{{ old('class_fee') }}" 
                                           step="0.01" 
                                           min="0" 
                                           required>
                                    @error('class_fee')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Max Students -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_students" class="form-label">
                                        {{ __('classes.max_students') }}
                                    </label>
                                    <input type="number" 
                                           class="form-control @error('max_students') is-invalid @enderror" 
                                           id="max_students" 
                                           name="max_students" 
                                           value="{{ old('max_students') }}" 
                                           min="1">
                                    @error('max_students')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Status -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">
                                        {{ __('classes.class_status') }} <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            id="status" 
                                            name="status" 
                                            required>
                                        <option value="registration" {{ old('status') == 'registration' ? 'selected' : '' }}>
                                            {{ __('classes.registration') }}
                                        </option>
                                        <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>
                                            {{ __('common.in_progress') }}
                                        </option>
                                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>
                                            {{ __('common.completed') }}
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Instructor -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="instructor_name" class="form-label">
                                        {{ __('classes.instructor_name') }}
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('instructor_name') is-invalid @enderror" 
                                           id="instructor_name" 
                                           name="instructor_name" 
                                           value="{{ old('instructor_name') }}">
                                    @error('instructor_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">
                                {{ __('common.description') }}
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('classes.index') }}" class="btn btn-secondary">
                                {{ __('common.cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                {{ __('classes.save_class') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate class code based on course selection
    const courseSelect = document.getElementById('course_id');
    const classCodeInput = document.getElementById('class_code');
    
    courseSelect.addEventListener('change', function() {
        if (this.value && !classCodeInput.value) {
            const courseText = this.options[this.selectedIndex].text;
            const courseArabic = courseText.split(' - ')[0];
            // Generate a simple code based on first letters
            const words = courseArabic.split(' ');
            let code = '';
            words.forEach(word => {
                if (word.length > 0) {
                    code += word.charAt(0);
                }
            });
            // Add timestamp to make it unique
            const timestamp = Date.now().toString().slice(-4);
            classCodeInput.value = code + '-' + timestamp;
        }
    });

    // Validate end date is after start date
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    
    startDateInput.addEventListener('change', function() {
        endDateInput.min = this.value;
        if (endDateInput.value && endDateInput.value < this.value) {
            endDateInput.value = '';
        }
    });

    // Cascading dropdown: Department -> Courses
    const departmentSelect = document.getElementById('category_id');
    // courseSelect already declared above
    
    departmentSelect.addEventListener('change', function() {
        const departmentId = this.value;
        console.log('Department changed:', departmentId);
        
        // Clear current courses
        courseSelect.innerHTML = '<option value="">{{ __("common.loading") }}...</option>';
        courseSelect.disabled = true;
        
        if (!departmentId) {
            courseSelect.innerHTML = '<option value="">{{ __("classes.first_select_department") }}</option>';
            return;
        }
        
        // Fetch courses for selected department
        const url = `{{ route('api.courses-by-category') }}?category_id=${departmentId}`;
        console.log('Fetching from:', url);
        
        fetch(url)
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(courses => {
                console.log('Courses received:', courses);
                courseSelect.innerHTML = '<option value="">{{ __("common.select_course") }}</option>';
                
                if (courses.length > 0) {
                    courses.forEach(course => {
                        const option = document.createElement('option');
                        option.value = course.id;
                        option.textContent = `${course.name_ar || course.name} - ${course.name_en || course.name}`;
                        courseSelect.appendChild(option);
                    });
                    courseSelect.disabled = false;
                } else {
                    courseSelect.innerHTML = '<option value="">{{ __("classes.no_courses_in_department") }}</option>';
                }
            })
            .catch(error => {
                console.error('Error fetching courses:', error);
                courseSelect.innerHTML = '<option value="">{{ __("common.error_loading") }}</option>';
            });
    });

    // Trigger change event on page load if department is already selected
    if (departmentSelect.value) {
        console.log('Triggering change for pre-selected department');
        departmentSelect.dispatchEvent(new Event('change'));
    }

    // Load Moodle courses on page load
    const moodleCourseSelect = document.getElementById('moodle_course_id');
    
    function loadMoodleCourses() {
        fetch('{{ route("classes.moodle-courses") }}', {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Moodle courses data:', data);
                if (data.success && data.data && data.data.length > 0) {
                    moodleCourseSelect.innerHTML = '<option value="">{{ __("classes.select_moodle_course") }}</option>';
                    data.data.forEach(course => {
                        const option = document.createElement('option');
                        option.value = course.id;
                        option.textContent = `${course.fullname} (ID: ${course.id})`;
                        moodleCourseSelect.appendChild(option);
                    });
                    console.log('Loaded ' + data.data.length + ' Moodle courses');
                } else {
                    console.warn('No courses in response or success=false', data);
                    moodleCourseSelect.innerHTML = '<option value="">{{ __("classes.no_moodle_courses") }}</option>';
                }
            })
            .catch(error => {
                console.error('Error loading Moodle courses:', error);
                moodleCourseSelect.innerHTML = '<option value="">{{ __("classes.error_loading_moodle") }}</option>';
            });
    }
    
    // Load Moodle courses when page loads
    loadMoodleCourses();
});
</script>
@endsection