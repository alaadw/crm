@extends('layouts.app')

@section('title', '{{ __("follow_ups.my_follow_ups") }} - CRM Academy')

@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">
                <i class="fas fa-tasks me-2"></i>
                {{ __('follow_ups.my_follow_ups') }}
            </h1>
            <div class="btn-group">
                <a href="{{ route('follow-ups.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    {{ __('follow_ups.add_follow_up') }}
                </a>
            </div>
        </div>

        <!-- Alert Notification -->
        @if($stats['today'] > 0 || $stats['overdue'] > 0)
        <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-bell me-2"></i>
            <strong>{{ __('follow_ups.daily_reminder') }}:</strong>
            {{ __('follow_ups.you_have') }} <span class="badge bg-primary">{{ $stats['today'] }}</span> {{ __('follow_ups.follow_ups_due_today') }}
            @if($stats['overdue'] > 0)
                {{ __('follow_ups.and') }} <span class="badge bg-danger">{{ $stats['overdue'] }}</span> {{ __('follow_ups.overdue_follow_ups_text') }}
            @endif
            .
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body text-center">
                        <i class="fas fa-clock fa-2x mb-2"></i>
                        <h4 class="card-title">{{ $stats['today'] }}</h4>
                        <p class="card-text">{{ __('follow_ups.due_today') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-danger">
                    <div class="card-body text-center">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <h4 class="card-title">{{ $stats['overdue'] }}</h4>
                        <p class="card-text">{{ __('follow_ups.overdue') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body text-center">
                        <i class="fas fa-user-check fa-2x mb-2"></i>
                        <h4 class="card-title">{{ $stats['by_status']['Expected to Register'] ?? 0 }}</h4>
                        <p class="card-text">{{ __('follow_ups.expected_to_register') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body text-center">
                        <i class="fas fa-pause fa-2x mb-2"></i>
                        <h4 class="card-title">{{ $stats['by_status']['Postponed'] ?? 0 }}</h4>
                        <p class="card-text">{{ __('follow_ups.postponed') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Tasks -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-calendar-day me-2"></i>
                    {{ __('follow_ups.todays_tasks') }}
                    <span class="badge bg-primary ms-2">{{ $todayFollowUps->count() }}</span>
                </h5>
            </div>
            <div class="card-body">
                @if($todayFollowUps->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>{{ __('follow_ups.priority') }}</th>
                                    <th>{{ __('follow_ups.student') }}</th>
                                    <th>{{ __('follow_ups.course') }}</th>
                                    <th>{{ __('follow_ups.phone') }}</th>
                                    <th>{{ __('common.status') }}</th>
                                    <th>{{ __('follow_ups.last_note') }}</th>
                                    <th>{{ __('follow_ups.next_follow_up') }}</th>
                                    <th>{{ __('common.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($todayFollowUps as $followUp)
                                <tr class="{{ $followUp->status === 'Expected to Register' ? 'table-success' : '' }}">
                                    <td>
                                        <span class="badge bg-{{ $followUp->priority_color }}">
                                            {{ $followUp->priority_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('students.show', $followUp->student) }}" class="text-decoration-none">
                                            <strong>{{ $followUp->student->full_name }}</strong>
                                        </a>
                                    </td>
                                    <td>
                                        @if($followUp->course)
                                            <span class="badge bg-info text-dark">
                                                {{ $followUp->course->name_ar }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="tel:{{ $followUp->student->phone_primary }}" class="text-decoration-none">
                                            <i class="fas fa-phone me-1"></i>
                                            {{ $followUp->student->formatted_phone_primary }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $followUp->status_color }}">
                                            {{ $followUp->status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <small>{{ Str::limit($followUp->action_note, 50) }}</small>
                                    </td>
                                    <td>
                                        @if($followUp->next_follow_up_date)
                                            <small>{{ $followUp->next_follow_up_date->format('M d, Y') }}</small>
                                        @else
                                            <span class="text-muted">{{ __('follow_ups.not_set') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="tel:{{ $followUp->student->phone_primary }}" 
                                               class="btn btn-outline-success" title="{{ __('follow_ups.call') }}">
                                                <i class="fas fa-phone"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-primary" 
                                                    title="{{ __('follow_ups.add_follow_up') }}"
                                                    onclick="openQuickFollowUp({{ $followUp->student->id }})">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                            <a href="{{ route('students.show', $followUp->student) }}" 
                                               class="btn btn-outline-info" title="{{ __('follow_ups.view_student') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h5 class="text-muted">{{ __('follow_ups.great_job_no_follow_ups') }}</h5>
                        <p class="text-muted">{{ __('follow_ups.all_caught_up') }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Overdue Follow-ups -->
        @if($overdueFollowUps->count() > 0)
        <div class="card mb-4">
            <div class="card-header bg-danger text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ __('follow_ups.overdue_follow_ups') }}
                    <span class="badge bg-light text-dark ms-2">{{ $overdueFollowUps->count() }}</span>
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>{{ __('follow_ups.priority') }}</th>
                                <th>{{ __('follow_ups.student') }}</th>
                                <th>{{ __('follow_ups.course') }}</th>
                                <th>{{ __('follow_ups.phone') }}</th>
                                <th>{{ __('common.status') }}</th>
                                <th>{{ __('follow_ups.last_note') }}</th>
                                <th>{{ __('follow_ups.due_date') }}</th>
                                <th>{{ __('follow_ups.days_overdue') }}</th>
                                <th>{{ __('common.actions') }}</th>
                            </tr>
                        </thead>
                        </thead>
                        <tbody>
                            @foreach($overdueFollowUps as $followUp)
                            <tr class="table-warning">
                                <td>
                                    <span class="badge bg-{{ $followUp->priority_color }}">
                                        {{ $followUp->priority_label }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('students.show', $followUp->student) }}" class="text-decoration-none">
                                        <strong>{{ $followUp->student->full_name }}</strong>
                                    </a>
                                </td>
                                <td>
                                    @if($followUp->course)
                                        <span class="badge bg-info text-dark">
                                            {{ $followUp->course->name_ar }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="tel:{{ $followUp->student->phone_primary }}" class="text-decoration-none">
                                        <i class="fas fa-phone me-1"></i>
                                        {{ $followUp->student->formatted_phone_primary }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $followUp->status_color }}">
                                        {{ $followUp->status_label }}
                                    </span>
                                </td>
                                <td>
                                    <small>{{ Str::limit($followUp->action_note, 50) }}</small>
                                </td>
                                <td>
                                    <small>{{ $followUp->next_follow_up_date->format('M d, Y') }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-danger">
                                        {{ $followUp->next_follow_up_date->diffInDays(now()) }} {{ __('follow_ups.days') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="tel:{{ $followUp->student->phone_primary }}" 
                                           class="btn btn-outline-success" title="{{ __('follow_ups.call') }}">
                                            <i class="fas fa-phone"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-primary" 
                                                title="{{ __('follow_ups.add_follow_up') }}"
                                                onclick="openQuickFollowUp({{ $followUp->student->id }})">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        <a href="{{ route('students.show', $followUp->student) }}" 
                                           class="btn btn-outline-info" title="{{ __('follow_ups.view_student') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Quick Follow-up Modal -->
<div class="modal fade" id="quickFollowUpModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('follow_ups.quick_follow_up') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="quickFollowUpForm">
                @csrf
                <input type="hidden" id="modal_student_id" name="student_id">
                <div class="modal-body">
                    <!-- Form content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('follow_ups.save_follow_up') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openQuickFollowUp(studentId) {
    $('#modal_student_id').val(studentId);
    
    // Load the form content via AJAX
    $.get(`{{ route('follow-ups.create') }}?student_id=${studentId}`)
        .done(function(response) {
            $('#quickFollowUpModal .modal-body').html($(response).find('.modal-body').html());
            $('#quickFollowUpModal').modal('show');
        });
}

$('#quickFollowUpForm').on('submit', function(e) {
    e.preventDefault();
    
    $.ajax({
        url: '{{ route("follow-ups.quick-add") }}',
        method: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            if (response.success) {
                $('#quickFollowUpModal').modal('hide');
                location.reload(); // Refresh the page to show updated data
            }
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                // Show validation errors
                const errors = xhr.responseJSON.errors;
                $('.invalid-feedback').remove();
                $('.is-invalid').removeClass('is-invalid');
                
                for (const [field, messages] of Object.entries(errors)) {
                    const input = $(`[name="${field}"]`);
                    input.addClass('is-invalid');
                    input.after(`<div class="invalid-feedback">${messages[0]}</div>`);
                }
            }
        }
    });
});

// Handle status change in quick form
$(document).on('change', '#modal_status', function() {
    const status = $(this).val();
    const nextFollowUpGroup = $('#next_follow_up_group');
    const cancellationGroup = $('#cancellation_group');
    
    if (status === 'Postponed') {
        nextFollowUpGroup.show().find('input').prop('required', true);
        cancellationGroup.hide().find('select, textarea').prop('required', false);
    } else if (status === 'Cancelled') {
        cancellationGroup.show().find('select').prop('required', true);
        nextFollowUpGroup.hide().find('input').prop('required', false);
    } else if (status === 'Expected to Register') {
        nextFollowUpGroup.show().find('input').prop('required', false);
        cancellationGroup.hide().find('select, textarea').prop('required', false);
    } else {
        nextFollowUpGroup.hide().find('input').prop('required', false);
        cancellationGroup.hide().find('select, textarea').prop('required', false);
    }
});
</script>
@endpush