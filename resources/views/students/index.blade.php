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
                <form method="GET" action="{{ route('students.index') }}" id="filterForm" class="row g-3 align-items-end">
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
                        <select name="course" id="course" class="form-select" {{ request('department') ? '' : 'disabled' }}>
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
                    @if(($assignableUsers ?? collect())->isNotEmpty() && ($showBulkAssignmentTools ?? false || $canChooseAssignedUser ?? false))
                    <div class="col-md-3">
                        <label for="filter_assigned_user_id" class="form-label">{{ __('students.assigned_user') }}</label>
                        <select name="assigned_user_id" id="filter_assigned_user_id" class="form-select">
                            <option value="">{{ __('common.all') }}</option>
                            @foreach($assignableUsers as $assignUser)
                                <option value="{{ $assignUser->id }}" {{ (string)request('assigned_user_id') === (string)$assignUser->id ? 'selected' : '' }}>
                                    {{ $assignUser->name }} @if($assignUser->email) ({{ $assignUser->email }}) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="col-md-3 d-flex align-items-center">
                        <button type="submit" class="btn btn-outline-primary me-2 w-100">
                            <i class="fas fa-search me-1"></i>
                            {{ __('students.search_filter') }}
                        </button>
                        @if(request()->hasAny(['search', 'department', 'course', 'assigned_user_id']))
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
                    <div class="col-md-4">
                        <label for="file" class="form-label" title="{{ __('students.full_name') }} (AR), {{ __('students.primary_phone') }} (JO), {{ __('students.college') }}, {{ __('students.major') }}">{{ __('common.import') }} <small class="text-black-50"> {{ __('students.full_name') }} (AR), {{ __('students.primary_phone') }} (JO), {{ __('students.college') }}, {{ __('students.major') }}</small></label>
                        <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" accept=".xlsx,.csv,.txt">
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @if(($assignableUsers ?? collect())->isNotEmpty() && ($canChooseAssignedUser ?? false))
                    <div class="col-md-3">
                        <label for="assigned_user_id" class="form-label">{{ __('students.import_assign_user') }}</label>
                        <select name="assigned_user_id" id="assigned_user_id" class="form-select @error('assigned_user_id') is-invalid @enderror">
                            <option value="">{{ __('students.import_assign_user_placeholder') }}</option>
                            @foreach($assignableUsers as $importUser)
                                <option value="{{ $importUser->id }}" {{ (string)old('assigned_user_id', $defaultAssignedUserId ?? null) === (string)$importUser->id ? 'selected' : '' }}>
                                    {{ $importUser->name }} @if($importUser->email) ({{ $importUser->email }}) @endif
                                </option>
                            @endforeach
                        </select>
                        @error('assigned_user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @endif
                    @if(!empty($importDepartments ?? []))
                    <div class="col-md-3">
                        <label for="department_category_id" class="form-label">{{ __('students.import_department') }}</label>
                        <select name="department_category_id" id="department_category_id" class="form-select @error('department_category_id') is-invalid @enderror">
                            <option value="">{{ __('students.import_department_placeholder') }}</option>
                            @foreach(($importDepartments ?? []) as $deptId => $deptName)
                                <option value="{{ $deptId }}" {{ (string)old('department_category_id', $defaultImportDepartmentId ?? null) === (string)$deptId ? 'selected' : '' }}>
                                    {{ $deptName }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @endif
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-outline-success w-100">
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
                    @if(request('assigned_user_id') && isset($selectedAssignedUser))
                        <span class="badge bg-secondary ms-2">
                            {{ __('students.assigned_user') }}: {{ $selectedAssignedUser->name ?? request('assigned_user_id') }}
                        </span>
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
                @php
                    $bulkAssignmentEnabled = !empty($showBulkAssignmentTools ?? false) && ($assignableUsers ?? collect())->isNotEmpty();
                    $oldStudentIds = collect(old('student_ids', []))->map(fn($id) => (int) $id)->all();
                @endphp

                @if($students->count() > 0)
                    @if($bulkAssignmentEnabled)
                        <form method="POST" action="{{ route('students.bulk-assign') }}" id="bulkAssignForm">
                            @csrf
                    @endif
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    @if($bulkAssignmentEnabled)
                                        <th class="text-center" style="width: 50px;">
                                            <div class="form-check m-0">
                                                <input type="checkbox" class="form-check-input" id="select_all_students">
                                                <label class="visually-hidden" for="select_all_students">{{ __('students.bulk_assign_select_all') }}</label>
                                            </div>
                                        </th>
                                    @endif
                                    <th>{{ __('students.student_id') }}</th>
                                    <th>{{ __('students.full_name') }}</th>
                                    <th>{{ __('students.phone') }}</th>
                            
                                    <th>{{ __('students.department') }}</th>
                                    <th>{{ __('students.assigned_user') }}</th>
                                    <th>{{ __('students.preferred_course') }}</th>
                                    <th>{{ __('students.reach_source') }}</th>
                                    <th>{{ __('students.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $student)
                                <tr>
                                    @if($bulkAssignmentEnabled)
                                        <td class="text-center">
                                            @if(is_null($student->assigned_user_id))
                                                <div class="form-check m-0">
                                                    <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="form-check-input student-select-checkbox" id="student_select_{{ $student->id }}" {{ in_array($student->id, $oldStudentIds, true) ? 'checked' : '' }}>
                                                    <label class="visually-hidden" for="student_select_{{ $student->id }}">{{ __('students.student') }} #{{ $student->id }}</label>
                                                </div>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    @endif
                                    <td>
                                        <code>{{ $student->student_id }}</code>
                                    </td>
                                    <td>
                                        <a href="{{ route('students.show', $student) }}" 
                                           class="text-dark text-decoration-none" title="{{ __('students.view') }}">
                                            <strong>{{ $student->full_name ?? $student->full_name_en }}</strong>
                                        </a>
                                        @if($student->full_name_en && $student->full_name_en !== ($student->full_name ?? $student->full_name_en))
                                            <br><small class="text-muted">{{ $student->full_name_en }}</small>
                                        @endif
                                    </td>
                                    <td>
                                       <a href="tel:{{ $student->phone_primary }}" class="text-dark text-decoration-none" title="{{ __('students.call') }}">
                                           <i class="fas fa-phone me-1"></i>
                                           {{ $student->formatted_phone_primary }}
                                       </a>
                                        @if($student->phone_alt)
                                            <br><small class="text-muted">
                                                {{ __('students.alt') }}: {{ $student->formatted_phone_alt }}
                                            </small>
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
                                        @if($student->assignedUser)
                                            {{ $student->assignedUser->name }}
                                            @if($student->assignedUser->email)
                                                <br><small class="text-muted">{{ $student->assignedUser->email }}</small>
                                            @endif
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
                                               class="btn btn-sm btn-outline-primary m-1" title="{{ __('students.view') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('students.edit', $student) }}" 
                                               class="btn btn-sm btn-outline-warning m-1" title="{{ __('students.edit') }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($bulkAssignmentEnabled)
                        <div class="mt-3 p-3 bg-light border rounded">
                            <div class="d-flex flex-column flex-lg-row align-items-lg-end gap-3">
                                <div class="flex-grow-1">
                                    <h6 class="mb-2">
                                        <i class="fas fa-user-check me-1"></i>
                                        {{ __('students.bulk_assign_heading') }}
                                    </h6>
                                    <p class="text-muted small mb-0">{{ __('students.bulk_assign_instruction') }}</p>
                                </div>
                                <div class="flex-grow-1">
                                    <label for="bulk_assigned_user_id" class="form-label">{{ __('students.assigned_user') }}</label>
                                    <select name="assigned_user_id" id="bulk_assigned_user_id" class="form-select @error('assigned_user_id') is-invalid @enderror">
                                        <option value="">{{ __('students.assigned_user_placeholder') }}</option>
                                        @foreach($assignableUsers as $assignUser)
                                            <option value="{{ $assignUser->id }}" {{ (string)old('assigned_user_id') === (string)$assignUser->id ? 'selected' : '' }}>
                                                {{ $assignUser->name }} @if($assignUser->email) ({{ $assignUser->email }}) @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('assigned_user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="flex-grow-1">
                                    <div class="text-muted small mb-2">
                                        {{ __('students.bulk_assign_warning_unassigned') }}
                                    </div>
                                    @error('student_ids')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                    <div class="text-muted small">
                                        {{ __('common.total') }}: <span id="selectedStudentsCount">0</span>
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <button type="submit" class="btn btn-success" id="bulkAssignSubmit" disabled>
                                        <i class="fas fa-user-plus me-1"></i>
                                        {{ __('students.bulk_assign_submit') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        </form>
                    @endif

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
    $('#search').on('keypress', function(e) {
        if (e.which === 13) {
            $(this).closest('form').submit();
        }
    });

    $('#department').on('change', function() {
        const departmentId = $(this).val();
        const courseSelect = $('#course');

        courseSelect.html('<option value="">{{ __('students.all_courses') }}</option>');
        if (!departmentId) {
            courseSelect.prop('disabled', true);
            return;
        }

        if (isNaN(departmentId)) {
            courseSelect.prop('disabled', true);
            courseSelect.html('<option value="">{{ __('students.not_available_legacy') }}</option>');
            return;
        }

        courseSelect.prop('disabled', false);
        courseSelect.html('<option value="">{{ __('common.loading') }}</option>');

        $.ajax({
            url: '{{ route('courses.by-department') }}',
            method: 'GET',
            data: { department: departmentId },
            success: function(courses) {
                courseSelect.html('<option value="">{{ __('students.all_courses') }}</option>');
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
                    courseSelect.append('<option value="">{{ __('students.no_courses_available') }}</option>');
                }

                const selectedCourse = @json(request('course'));
                if (selectedCourse) {
                    courseSelect.val(selectedCourse);
                }
            },
            error: function() {
                courseSelect.html('<option value="">{{ __('common.error_loading') }}</option>');
            }
        });
    });

    @if(request('department'))
        $('#department').trigger('change');
    @endif

    const bulkForm = $('#bulkAssignForm');
    if (bulkForm.length) {
        const selectAll = $('#select_all_students');
        const studentCheckboxes = $('.student-select-checkbox');
        const summaryCount = $('#selectedStudentsCount');
        const submitButton = $('#bulkAssignSubmit');

        const updateSummary = () => {
            const selectedCount = studentCheckboxes.filter(':checked').length;
            summaryCount.text(selectedCount);
            submitButton.prop('disabled', selectedCount === 0);
        };

        selectAll.on('change', function() {
            const shouldSelect = $(this).is(':checked');
            studentCheckboxes.filter(function() {
                return !this.disabled;
            }).prop('checked', shouldSelect);
            updateSummary();
        });

        studentCheckboxes.on('change', function() {
            const totalSelectable = studentCheckboxes.length;
            const selectedCount = studentCheckboxes.filter(':checked').length;
            selectAll.prop('checked', selectedCount > 0 && selectedCount === totalSelectable);
            updateSummary();
        });

        updateSummary();
    }
});
</script>
@endpush