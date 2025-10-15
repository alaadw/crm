@extends('layouts.app')

@section('title', __("students.students") . ' - CRM Academy')

@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">
                <i class="fas fa-users me-2"></i>
                {{ __('students.students_management') }}
            </h1>
            <a href="{{ route('students.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>
                {{ __('students.add_new_student') }}
            </a>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-users fa-2x me-3"></i>
                            <div>
                                <h5 class="card-title mb-0">{{ $stats['total'] ?? 0 }}</h5>
                                <p class="card-text">{{ __('students.total_students') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            @php
                $departmentColors = ['bg-info', 'bg-success', 'bg-warning', 'bg-secondary', 'bg-dark'];
                $colorIndex = 0;
            @endphp
            @foreach(($stats['by_department'] ?? []) as $deptName => $count)
                @if($deptName && $count > 0)
                <div class="col-md-2">
                    <div class="card text-white {{ $departmentColors[$colorIndex % count($departmentColors)] }}">
                        <div class="card-body text-center">
                            <h5 class="card-title mb-0">{{ $count }}</h5>
                            <p class="card-text small">{{ $deptName }}</p>
                        </div>
                    </div>
                </div>
                @php $colorIndex++; @endphp
                @endif
            @endforeach
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('students.index') }}" id="filterForm" class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">{{ __('students.search') }}</label>
                        <input type="text" name="search" id="search" class="form-control" 
                               value="{{ request('search') }}" 
                               placeholder="{{ __('students.search_placeholder') }}" title="{{ __('students.search_tooltip') }}">
                         
                    </div>
                    <div class="col-md-3">
                        <label for="department" class="form-label">{{ __('students.filter_by_department') }}</label>
                        <select name="department" id="department" class="form-select">
                            <option value="">{{ __('students.all_departments') }}</option>
                            @foreach($departments as $deptKey => $deptName)
                                <option value="{{ $deptKey }}" {{ request('department') == $deptKey ? 'selected' : '' }}>
                                    {{ $deptName }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="course" class="form-label">{{ __('students.filter_by_course') }}</label>
                        <select name="course" id="course" class="form-select">
                            <option value="">{{ __('students.all_courses') }}</option>
                            @if(isset($courses) && count($courses) > 0)
                                @foreach($courses as $courseItem)
                                    <option value="{{ $courseItem['id'] }}" {{ request('course') == $courseItem['id'] ? 'selected' : '' }}>
                                        {{ $courseItem['name'] }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-outline-primary me-2">
                            <i class="fas fa-search me-1"></i>
                            {{ __('students.search_filter') }}
                        </button>
                        @if(request('search') || request('department') || request('course'))
                            <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>
                                {{ __('students.clear') }}
                            </a>
                        @endif
                    </div>
                </form>
                <hr>
                <form method="POST" action="{{ route('students.import') }}" enctype="multipart/form-data" class="row g-3 mt-1">
                    @csrf
                    <div class="col-md-6">
                        <label for="file" class="form-label" title="{{ __('students.full_name') }} (AR), {{ __('students.primary_phone') }} (JO), {{ __('students.college') }}, {{ __('students.major') }}">{{ __('common.import') }} <small class="text-black-50"> {{ __('students.full_name') }} (AR), {{ __('students.primary_phone') }} (JO), {{ __('students.college') }}, {{ __('students.major') }}</small></label>
                        <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" accept=".xlsx,.csv,.txt">
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text"></div>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-outline-success">
                            <i class="fas fa-file-import me-1"></i> {{ __('common.import') }}
                        </button>
                    </div>
                    @if(session('import_result'))
                    @php $r = session('import_result'); @endphp
                    <div class="col-12">
                        <div class="alert alert-info mt-2">
                            <strong>{{ __('common.import_completed') }}</strong>
                            — {{ __('common.total') }}: {{ $r['rows'] }}, {{ __('common.created') }}: {{ $r['created'] }}, {{ __('common.warning') }} ({{ __('students.not_specified') }}): {{ $r['invalid'] }}, {{ __('common.duplicate') ?? 'Duplicate' }}: {{ $r['duplicates'] }}
                            @if(!empty($r['errors']))
                                <ul class="mb-0 mt-2">
                                    @foreach($r['errors'] as $e)
                                        <li>{{ $e }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                    @endif
                </form>
            </div>
        </div>

        <!-- Students Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>
                    {{ __('students.students_list') }}
                    @if(session('status'))
                        <span class="badge bg-success ms-2">{{ session('status') }}</span>
                    @endif
                    @if(request('department'))
                        <span class="badge bg-primary ms-2">{{ $departments[request('department')] ?? request('department') }}</span>
                    @endif
                    @if(request('course') && isset($courses))
                        @php
                            $selectedCourse = collect($courses)->firstWhere('id', request('course'));
                        @endphp
                        @if($selectedCourse)
                            <span class="badge bg-success ms-2">{{ $selectedCourse['name'] }}</span>
                        @endif
                    @endif
                    @if(request('search'))
                        <span class="badge bg-info ms-2">
                            {{ __('students.search_results_for') }}: "{{ request('search') }}"
                        </span>
                    @endif
                </h5>
            </div>
            <div class="card-body">
                @if(session('import_result'))
                    @php $r = session('import_result'); @endphp
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-1"></i>
                        {{ __('common.import_completed') }} — {{ __('common.total') }}: {{ $r['rows'] }}, {{ __('common.created') }}: {{ $r['created'] }}, {{ __('common.warning') }}: {{ $r['invalid'] }}, {{ __('common.duplicate') ?? 'Duplicate' }}: {{ $r['duplicates'] }}
                    </div>
                @endif
                @if($students->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>{{ __('students.student_id') }}</th>
                                    <th>{{ __('students.full_name') }}</th>
                                    <th>{{ __('students.phone') }}</th>
                                    <th>{{ __('students.email') }}</th>
                                    <th>{{ __('students.department') }}</th>
                                    <th>{{ __('students.preferred_course') }}</th>
                                    <th>{{ __('students.reach_source') }}</th>
                                    <th>{{ __('students.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $student)
                                <tr>
                                    <td>
                                        <code>{{ $student->student_id }}</code>
                                    </td>
                                    <td>
                                        @if($student->full_name_en)
                                        
                                        
                                            <a href="{{ route('students.show', $student) }}" 
                                               class="text-dark text-decoration-none" title="{{ __('students.show') }}">
                                               <strong>{{ $student->full_name ?? $student->full_name_en }}</strong>
                                       @endif        
                                    </td>
                                    <td>
                                        <i class="fas fa-phone me-1"></i>
                                        {{ $student->formatted_phone_primary }}
                                        @if($student->phone_alt)
                                            <br><small class="text-muted">
                                                {{ __('students.alt') }}: {{ $student->formatted_phone_alt }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($student->email)
                                            <i class="fas fa-envelope me-1"></i>
                                            {{ $student->email }}
                                        @else
                                            <span class="text-muted">{{ __('students.not_provided') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($student->departmentCategory)
                                            <span class="badge bg-info">{{ $student->departmentCategory->name }}</span>
                                            @if($student->departmentCategory->name_en !== $student->departmentCategory->name)
                                                <br><small class="text-muted">{{ $student->departmentCategory->name_en }}</small>
                                            @endif
                                        @elseif($student->department)
                                            <span class="badge bg-warning">{{ $student->department }}</span>
                                            <br><small class="text-muted">{{ __('students.legacy') }}</small>
                                        @else
                                            <span class="text-muted">{{ __('students.not_assigned') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($student->preferredCourse)
                                            <span class="badge bg-success">
                                                {{ $student->preferredCourse->name_ar ?? $student->preferredCourse->name }}
                                                @if($student->preferredCourse->name_en && $student->preferredCourse->name_en !== ($student->preferredCourse->name_ar ?? $student->preferredCourse->name))
                                                    <br><small class="text-white-50">{{ $student->preferredCourse->name_en }}</small>
                                                @endif
                                            </span>
                                        @else
                                            <span class="text-muted">{{ __('students.not_specified') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $student->reach_source_label }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('students.show', $student) }}" 
                                               class="btn btn-sm btn-outline-primary" title="{{ __('students.view') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('students.edit', $student) }}" 
                                               class="btn btn-sm btn-outline-warning" title="{{ __('students.edit') }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if(method_exists($students, 'links'))
                        <div class="d-flex justify-content-center mt-3">
                            {{ $students->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">{{ __('students.no_students_found') }}</h5>
                        <p class="text-muted">
                            @if(request('search'))
                                {{ __('students.no_search_results', ['search' => request('search')]) }}
                            @elseif(request('department'))
                                {{ __('students.no_students_in_department', ['department' => $departments[request('department')] ?? request('department')]) }}
                            @else
                                {{ __('students.start_by_adding') }}
                            @endif
                        </p>
                        @if(!request('search') && !request('department'))
                            <a href="{{ route('students.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>
                                {{ __('students.add_new_student') }}
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-submit form on enter key in search field
    $('#search').on('keypress', function(e) {
        if (e.which == 13) { // Enter key
            $(this).closest('form').submit();
        }
    });
    
    // Clear search when clear button is clicked
    $('a[href*="students.index"]:contains("{{ __("students.clear") }}")').on('click', function(e) {
        e.preventDefault();
        window.location.href = '{{ route("students.index") }}';
    });
    
    // Add visual feedback for search results
    @if(request('search'))
        $('#search').addClass('border-info');
    @endif
    
    // Dynamic course loading based on department selection
    $('#department').on('change', function() {
        const departmentId = $(this).val();
        const courseSelect = $('#course');
        
        // Clear current courses
        courseSelect.html('<option value="">{{ __("students.all_courses") }}</option>');
        
        // If no department selected, disable course select
        if (!departmentId) {
            courseSelect.prop('disabled', true);
            return;
        }
        
        // If department is not numeric (legacy), disable course select
        if (isNaN(departmentId)) {
            courseSelect.prop('disabled', true);
            courseSelect.html('<option value="">{{ __("students.not_available_legacy") }}</option>');
            return;
        }
        
        // Enable course select and show loading
        courseSelect.prop('disabled', false);
        courseSelect.html('<option value="">{{ __("common.loading") }}...</option>');
        
        // Fetch courses for selected department
        $.ajax({
            url: '{{ route("courses.by-department") }}',
            method: 'GET',
            data: { department: departmentId },
            success: function(courses) {
                courseSelect.html('<option value="">{{ __("students.all_courses") }}</option>');
                
                if (courses.length > 0) {
                    courses.forEach(function(course) {
                        const optionText = course.name_ar || course.name_en || course.name;
                        courseSelect.append(
                            $('<option>', {
                                value: course.id,
                                text: optionText
                            })
                        );
                    });
                } else {
                    courseSelect.append('<option value="">{{ __("students.no_courses_available") }}</option>');
                }
                
                // Restore selected course if exists
                const selectedCourse = '{{ request("course") }}';
                if (selectedCourse) {
                    courseSelect.val(selectedCourse);
                }
            },
            error: function() {
                courseSelect.html('<option value="">{{ __("common.error_loading") }}</option>');
            }
        });
    });
    
    // Trigger change event on page load if department is selected
    @if(request('department'))
        $('#department').trigger('change');
    @endif
});
</script>
@endpush