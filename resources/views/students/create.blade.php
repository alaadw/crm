@extends('layouts.app')

@section('title', __('students.add_new_student') . ' - CRM Academy')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <!-- Page Header -->
        <div class="d-flex align-items-center mb-4">
            <a href="{{ route('students.index') }}" class="btn btn-outline-secondary me-3">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="h3 mb-0">
                <i class="fas fa-user-plus me-2"></i>
                {{ __('students.add_new_student') }}
            </h1>
        </div>

        <!-- Student Form -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('students.student_information') }}</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('students.store') }}">
                    @csrf

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
                                   id="full_name" name="full_name" value="{{ old('full_name') }}" required>
                            @error('full_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="full_name_en" class="form-label">
                                {{ __('students.full_name_en') }}
                            </label>
                            <input type="text" class="form-control @error('full_name_en') is-invalid @enderror" 
                                   id="full_name_en" name="full_name_en" value="{{ old('full_name_en') }}">
                            @error('full_name_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="email" class="form-label">{{ __('students.email') }}</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    @if(!empty($canChooseAssignedUser) && isset($assignableUsers) && $assignableUsers->isNotEmpty())
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="assigned_user_id" class="form-label">{{ __('students.assigned_user') }}</label>
                            <select name="assigned_user_id" id="assigned_user_id" class="form-select @error('assigned_user_id') is-invalid @enderror">
                                <option value="">{{ __('students.assigned_user_placeholder') }}</option>
                                @foreach($assignableUsers as $assignUser)
                                    <option value="{{ $assignUser->id }}" {{ (string)old('assigned_user_id', $defaultAssignedUserId) === (string)$assignUser->id ? 'selected' : '' }}>
                                        {{ $assignUser->name }}
                                        @if($assignUser->email)
                                            ({{ $assignUser->email }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    @endif

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
                                            {{ (old('country_code', '+962') === $code) ? 'selected' : '' }}>
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
                                   value="{{ old('phone_primary', session('prefill_phone')) }}" 
                                   placeholder="79XXXXXXX" required>
                            @error('phone_primary')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">{{ __('students.enter_without_country_code') }}</div>
                        </div>
                        <div class="col-md-5">
                            <label for="phone_alt" class="form-label">{{ __('students.alternative_phone') }}</label>
                            <input type="text" class="form-control @error('phone_alt') is-invalid @enderror" 
                                   id="phone_alt" name="phone_alt" value="{{ old('phone_alt') }}" 
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
                                {{ __('students.department') }} <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('department') is-invalid @enderror" 
                                    id="department" name="department" required>
                                <option value="">{{ __('students.select_department') }}</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept['id'] }}" {{ old('department') == $dept['id'] ? 'selected' : '' }}>
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
                                    <option value="{{ $category->id }}" 
                                            data-has-children="{{ $category->children->count() > 0 ? 1 : 0 }}">
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
                        <div class="col-md-4">
                            <label for="university" class="form-label">{{ __('students.university') }}</label>
                            <input type="text" class="form-control @error('university') is-invalid @enderror" id="university" name="university" value="{{ old('university') }}" placeholder="{{ __('students.university') }}">
                            @error('university')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="major" class="form-label">{{ __('students.major') }}</label>
                            <input type="text" class="form-control @error('major') is-invalid @enderror" id="major" name="major" value="{{ old('major') }}" placeholder="{{ __('students.major') }}">
                            @error('major')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="college" class="form-label">{{ __('students.college') }}</label>
                            <input type="text" class="form-control @error('college') is-invalid @enderror" id="college" name="college" value="{{ old('college') }}" placeholder="{{ __('students.college') }}">
                            @error('college')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                                    @php
                                        $key = strtolower(str_replace(' ', '_', $source));
                                        $label = __("students.$key");
                                        if ($label === "students.$key") { $label = $source; }
                                    @endphp
                                    <option value="{{ $source }}" {{ old('reach_source') === $source ? 'selected' : '' }}>
                                        {{ $label }}
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
                                      placeholder="{{ __('students.additional_notes_placeholder') }}">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('students.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i>
                                    {{ __('students.cancel') }}
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>
                                    {{ __('students.create_student') }}
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
                    $('#subcategory').append(
                        $('<option></option>')
                            .attr('value', subcategory.id)
                            .text(subcategory.name + (subcategory.name_en && subcategory.name_en !== subcategory.name ? ' - ' + subcategory.name_en : ''))
                    );
                });
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
                    $('#preferred_course_id').append(
                        $('<option></option>')
                            .attr('value', course.id)
                            .text(course.name + (course.name_en && course.name_en !== course.name ? ' - ' + course.name_en : ''))
                    );
                });
            })
            .fail(function() {
                console.error('Failed to load courses');
            });
    }
    
    // Filter courses by department (existing functionality)
    $('#department').change(function() {
        // This functionality can be kept for department-based filtering if needed
        // Currently focusing on category-based course selection
    });
});
</script>
@endpush