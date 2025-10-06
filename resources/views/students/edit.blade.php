@extends('layouts.app')

@section('title', '{{ __("students.edit_student") }} - ' . $student->full_name . ' - CRM Academy')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="d-flex align-items-center">
                <a href="{{ route('students.show', $student) }}" class="btn btn-outline-secondary me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-user-edit me-2"></i>
                        {{ __('students.edit_student') }}
                    </h1>
                    <small class="text-muted">{{ $student->full_name }} ({{ $student->student_id }})</small>
                </div>
            </div>
            
            <!-- Quick Stats -->
            <div class="d-flex gap-2">
                @php
                    $followUpsCount = $student->followUps()->count();
                    $pendingCount = $student->followUps()->where('status', 'pending')->count();
                @endphp
                <span class="badge bg-info">{{ $followUpsCount }} {{ __('follow_ups.follow_ups') }}</span>
                @if($pendingCount > 0)
                    <span class="badge bg-warning">{{ $pendingCount }} {{ __('follow_ups.pending') }}</span>
                @endif
            </div>
        </div>

        <!-- Student Form -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('students.student_information') }}</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('students.update', $student) }}">
                    @csrf
                    @method('PUT')

                    <!-- Basic Information -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h6 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-user me-2"></i>
                                {{ __('students.basic_information') }}
                            </h6>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="full_name" class="form-label">
                                {{ __('students.full_name') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('full_name') is-invalid @enderror" 
                                   id="full_name" name="full_name" 
                                   value="{{ old('full_name', $student->full_name) }}" required>
                            @error('full_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="full_name_en" class="form-label">
                                {{ __('students.full_name_en') }}
                            </label>
                            <input type="text" class="form-control @error('full_name_en') is-invalid @enderror" 
                                   id="full_name_en" name="full_name_en" 
                                   value="{{ old('full_name_en', $student->full_name_en) }}">
                            @error('full_name_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="email" class="form-label">{{ __('students.email') }}</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" 
                                   value="{{ old('email', $student->email) }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Phone Information -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h6 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-phone me-2"></i>
                                {{ __('students.contact_information') }}
                            </h6>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="country_code" class="form-label">
                                {{ __('students.country_code') }} <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('country_code') is-invalid @enderror" 
                                    id="country_code" name="country_code">
                                @foreach($countryCodes as $code => $country)
                                    <option value="{{ $code }}" 
                                            {{ (old('country_code', $student->country_code) === $code) ? 'selected' : '' }}>
                                        {{ $code }} ({{ $country }})
                                    </option>
                                @endforeach
                            </select>
                            @error('country_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="phone_primary" class="form-label">
                                {{ __('students.primary_phone') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('phone_primary') is-invalid @enderror" 
                                   id="phone_primary" name="phone_primary" 
                                   value="{{ old('phone_primary', $student->phone_primary) }}" 
                                   placeholder="79XXXXXXX" required>
                            @error('phone_primary')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                {{ __('students.changing_phone_notice') }}
                            </div>
                        </div>
                        <div class="col-md-5">
                            <label for="phone_alt" class="form-label">{{ __('students.alternative_phone') }}</label>
                            <input type="text" class="form-control @error('phone_alt') is-invalid @enderror" 
                                   id="phone_alt" name="phone_alt" 
                                   value="{{ old('phone_alt', $student->phone_alt) }}" 
                                   placeholder="{{ __('students.optional_alternative_number') }}">
                            @error('phone_alt')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Academic Information -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h6 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-graduation-cap me-2"></i>
                                {{ __('students.academic_information') }}
                            </h6>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="department" class="form-label">
                                {{ __('common.department') }} <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('department') is-invalid @enderror" 
                                    id="department" name="department" required>
                                <option value="">{{ __('common.select_department') }}</option>
                                @foreach($departments as $dept)
                                    @php
                                        $currentDepartment = old('department', $student->department_category_id ?? $student->department);
                                    @endphp
                                    <option value="{{ $dept['id'] }}" 
                                            {{ $currentDepartment == $dept['id'] ? 'selected' : '' }}>
                                        {{ $dept['name'] }}
                                        @if($dept['name_en'] !== $dept['name'])
                                            ({{ $dept['name_en'] }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('department')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="preferred_course_id" class="form-label">{{ __('students.preferred_course') }}</label>
                            
                            <!-- Main Category Selection -->
                            <select class="form-select mb-2" id="main_category" name="main_category">
                                <option value="">{{ __('students.select_main_category') }}</option>
                                @foreach($categories as $category)
                                    @php
                                        $isSelected = false;
                                        if ($student->preferredCourse && $student->preferredCourse->category) {
                                            $courseCategory = $student->preferredCourse->category;
                                            $isSelected = ($courseCategory->parent_id == 0 && $courseCategory->id == $category->id) ||
                                                         ($courseCategory->parent_id == $category->id);
                                        }
                                    @endphp
                                    <option value="{{ $category->id }}" 
                                            data-has-children="{{ $category->children->count() > 0 ? 1 : 0 }}"
                                            {{ $isSelected ? 'selected' : '' }}>
                                        {{ $category->name_ar ?? $category->name_en }}
                                        @if($category->name_en && $category->name_en !== ($category->name_ar ?? $category->name_en))
                                            - {{ $category->name_en }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            
                            <!-- Subcategory Selection (shown when main category has children) -->
                            <select class="form-select mb-2 d-none" id="subcategory" name="subcategory">
                                <option value="">{{ __('students.select_subcategory') }}</option>
                            </select>
                            
                            <!-- Course Selection -->
                            <select class="form-select @error('preferred_course_id') is-invalid @enderror" 
                                    id="preferred_course_id" name="preferred_course_id">
                                <option value="">{{ __('students.select_course_optional') }}</option>
                            </select>
                            @error('preferred_course_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">{{ __('students.leave_empty_if_undecided') }}</div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h6 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                {{ __('students.additional_information') }}
                            </h6>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="reach_source" class="form-label">
                                {{ __('students.how_did_they_reach_us') }} <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('reach_source') is-invalid @enderror" 
                                    id="reach_source" name="reach_source" required>
                                <option value="">{{ __('students.select_source') }}</option>
                                @foreach($reachSources as $source)
                                    <option value="{{ $source }}" 
                                            {{ old('reach_source', $student->reach_source) === $source ? 'selected' : '' }}>
                                        {{ $source }}
                                    </option>
                                @endforeach
                            </select>
                            @error('reach_source')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label for="notes" class="form-label">{{ __('students.notes') }}</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3" 
                                      placeholder="{{ __('students.any_additional_notes') }}">{{ old('notes', $student->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Student ID Info -->
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>{{ __('students.student_id') }}:</strong> {{ $student->student_id }} 
                        <small class="text-muted">({{ __('students.cannot_be_changed') }})</small>
                    </div>

                    <!-- Form Actions -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('students.show', $student) }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i>
                                    {{ __('common.cancel') }}
                                </a>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save me-1"></i>
                                    {{ __('students.update_student') }}
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
    // Store the current selected course for restoration
    const currentSelectedCourse = {{ old('preferred_course_id', $student->preferred_course_id) ?? 'null' }};
    
    // Handle main category change
    $('#main_category').change(function() {
        const selectedCategory = $(this).val();
        const hasChildren = $(this).find('option:selected').data('has-children');
        
        // Reset subcategory and courses
        $('#subcategory').addClass('d-none').val('').find('option:not(:first)').remove();
        $('#preferred_course_id').val('').find('option:not(:first)').remove();
        
        if (selectedCategory) {
            if (hasChildren) {
                // Load subcategories
                loadSubcategories(selectedCategory);
            } else {
                // Load courses directly
                loadCourses(selectedCategory);
            }
        }
    });
    
    // Handle subcategory change
    $('#subcategory').change(function() {
        const selectedSubcategory = $(this).val();
        
        // Reset courses
        $('#preferred_course_id').val('').find('option:not(:first)').remove();
        
        if (selectedSubcategory) {
            loadCourses(selectedSubcategory);
        }
    });
    
    // Function to load subcategories
    function loadSubcategories(parentId) {
        $.get('{{ route("api.subcategories") }}', { parent_id: parentId })
            .done(function(data) {
                $('#subcategory').removeClass('d-none');
                
                data.forEach(function(subcategory) {
                    const option = $('<option></option>')
                        .attr('value', subcategory.id)
                        .text(subcategory.name + (subcategory.name_en && subcategory.name_en !== subcategory.name ? ' - ' + subcategory.name_en : ''));
                    
                    $('#subcategory').append(option);
                });
                
                // Check if current course belongs to any of the subcategories
                @if($student->preferredCourse && $student->preferredCourse->category && $student->preferredCourse->category->parent_id > 0)
                    $('#subcategory').val({{ $student->preferredCourse->category->id }}).trigger('change');
                @endif
            })
            .fail(function() {
                console.error('Failed to load subcategories');
            });
    }
    
    // Function to load courses
    function loadCourses(categoryId) {
        $.get('{{ route("api.courses-by-category") }}', { category_id: categoryId })
            .done(function(data) {
                data.forEach(function(course) {
                    const option = $('<option></option>')
                        .attr('value', course.id)
                        .text(course.name + (course.name_en && course.name_en !== course.name ? ' - ' + course.name_en : ''));
                    
                    // Check if this was the previously selected course
                    if (course.id == currentSelectedCourse) {
                        option.prop('selected', true);
                    }
                    
                    $('#preferred_course_id').append(option);
                });
            })
            .fail(function() {
                console.error('Failed to load courses');
            });
    }
    
    // Initialize on page load
    const selectedMainCategory = $('#main_category').val();
    if (selectedMainCategory) {
        $('#main_category').trigger('change');
    }
});
</script>
@endpush