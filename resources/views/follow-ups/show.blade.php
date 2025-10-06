@extends('layouts.app')

@section('title', '{{ __("follow_ups.follow_up_details") }} - CRM Academy')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">
                <i class="fas fa-eye me-2"></i>
                {{ __('follow_ups.follow_up_details') }}
            </h1>
            <div>
                <a href="{{ route('follow-ups.student', $followUp->student) }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-list me-1"></i>
                    {{ __('follow_ups.back_to_follow_ups') }}
                </a>
                <a href="{{ route('students.show', $followUp->student) }}" class="btn btn-outline-primary me-2">
                    <i class="fas fa-user me-1"></i>
                    {{ __('follow_ups.back_to_student') }}
                </a>
                @if($followUp->status === 'pending')
                    <a href="{{ route('follow-ups.edit', $followUp) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i>
                        {{ __('common.edit') }}
                    </a>
                @endif
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
                            @if($followUp->student->email)
                                • <i class="fas fa-envelope me-1"></i>
                                {{ $followUp->student->email }}
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <span class="badge bg-info">{{ $followUp->student->student_id }}</span>
                        @if($followUp->student->departmentCategory)
                            <span class="badge bg-secondary">{{ $followUp->student->departmentCategory->name }}</span>
                        @elseif($followUp->student->department)
                            <span class="badge bg-warning">{{ $followUp->student->department }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Follow-up Details -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    {{ __('follow_ups.follow_up_information') }}
                </h5>
                <div>
                    @switch($followUp->status)
                        @case('completed')
                            <span class="badge bg-success fs-6">
                                <i class="fas fa-check me-1"></i>{{ __('follow_ups.completed') }}
                            </span>
                            @break
                        @case('cancelled')
                            <span class="badge bg-danger fs-6">
                                <i class="fas fa-times me-1"></i>{{ __('follow_ups.cancelled') }}
                            </span>
                            @break
                        @case('pending')
                            <span class="badge bg-warning fs-6">
                                <i class="fas fa-clock me-1"></i>{{ __('follow_ups.pending') }}
                            </span>
                            @break
                        @default
                            <span class="badge bg-secondary fs-6">{{ ucfirst($followUp->status) }}</span>
                    @endswitch
                    
                    @switch($followUp->priority)
                        @case('high')
                            <span class="badge bg-danger fs-6 ms-1">{{ __('follow_ups.high_priority') }}</span>
                            @break
                        @case('medium')
                            <span class="badge bg-warning fs-6 ms-1">{{ __('follow_ups.medium_priority') }}</span>
                            @break
                    @endswitch
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="140"><strong>{{ __('follow_ups.scheduled_date') }}:</strong></td>
                                <td>
                                    <i class="fas fa-calendar me-1"></i>
                                    {{ $followUp->scheduled_date->format('M j, Y') }}
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $followUp->scheduled_date->format('g:i A') }}
                                    </small>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('follow_ups.contact_method') }}:</strong></td>
                                <td>
                                    @switch($followUp->contact_method)
                                        @case('phone')
                                            <i class="fas fa-phone me-1"></i>{{ __('follow_ups.phone_call') }}
                                            @break
                                        @case('whatsapp')
                                            <i class="fab fa-whatsapp me-1"></i>{{ __('follow_ups.whatsapp') }}
                                            @break
                                        @case('email')
                                            <i class="fas fa-envelope me-1"></i>{{ __('follow_ups.email') }}
                                            @break
                                        @case('in_person')
                                            <i class="fas fa-user me-1"></i>{{ __('follow_ups.in_person') }}
                                            @break
                                        @default
                                            {{ ucfirst($followUp->contact_method) }}
                                    @endswitch
                                </td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('follow_ups.type') }}:</strong></td>
                                <td>
                                    <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $followUp->type)) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('follow_ups.related_course') }}:</strong></td>
                                <td>
                                    @if($followUp->course)
                                        <span class="badge bg-success">{{ $followUp->course->name_ar }}</span>
                                        <br><small class="text-muted">{{ $followUp->course->name_en }}</small>
                                    @else
                                        <span class="text-muted">{{ __('follow_ups.no_specific_course') }}</span>
                                    @endif
                                </td>
                            </tr>
                            @if($followUp->outcome)
                            <tr>
                                <td><strong>{{ __('follow_ups.outcome') }}:</strong></td>
                                <td>
                                    <span class="badge bg-secondary">{{ $followUp->outcome }}</span>
                                </td>
                            </tr>
                            @endif
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="140"><strong>{{ __('follow_ups.created_by') }}:</strong></td>
                                <td>
                                    <i class="fas fa-user me-1"></i>
                                    {{ $followUp->user->name ?? __('follow_ups.system') }}
                                </td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('follow_ups.created_at') }}:</strong></td>
                                <td>
                                    <i class="fas fa-calendar-plus me-1"></i>
                                    {{ $followUp->created_at->format('M j, Y g:i A') }}
                                </td>
                            </tr>
                            @if($followUp->updated_at->ne($followUp->created_at))
                            <tr>
                                <td><strong>{{ __('follow_ups.last_updated') }}:</strong></td>
                                <td>
                                    <i class="fas fa-edit me-1"></i>
                                    {{ $followUp->updated_at->format('M j, Y g:i A') }}
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <td><strong>{{ __('follow_ups.priority') }}:</strong></td>
                                <td>
                                    @switch($followUp->priority)
                                        @case('high')
                                            <span class="badge bg-danger">{{ __('follow_ups.high') }}</span>
                                            @break
                                        @case('medium')
                                            <span class="badge bg-warning">{{ __('follow_ups.medium') }}</span>
                                            @break
                                        @case('low')
                                            <span class="badge bg-secondary">{{ __('follow_ups.low') }}</span>
                                            @break
                                    @endswitch
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Purpose -->
                <div class="mt-4">
                    <h6 class="border-bottom pb-2">
                        <i class="fas fa-bullseye me-2"></i>
                        {{ __('follow_ups.purpose_agenda') }}
                    </h6>
                    <p class="mb-3">{{ $followUp->purpose }}</p>
                </div>

                <!-- Notes -->
                @if($followUp->notes)
                <div class="mt-4">
                    <h6 class="border-bottom pb-2">
                        <i class="fas fa-sticky-note me-2"></i>
                        {{ __('follow_ups.additional_notes') }}
                    </h6>
                    <p class="mb-3">{{ $followUp->notes }}</p>
                </div>
                @endif

                <!-- Action Notes -->
                @if($followUp->action_note)
                <div class="mt-4">
                    <h6 class="border-bottom pb-2">
                        <i class="fas fa-tasks me-2"></i>
                        {{ __('follow_ups.action_notes') }}
                    </h6>
                    <p class="mb-3">{{ $followUp->action_note }}</p>
                </div>
                @endif

                <!-- Cancellation Details -->
                @if($followUp->status === 'cancelled')
                <div class="mt-4">
                    <h6 class="border-bottom pb-2 text-danger">
                        <i class="fas fa-times-circle me-2"></i>
                        {{ __('follow_ups.cancellation_details') }}
                    </h6>
                    <div class="row">
                        @if($followUp->cancellation_reason)
                        <div class="col-md-6">
                            <strong>{{ __('follow_ups.reason') }}:</strong>
                            <span class="badge bg-danger ms-2">{{ $followUp->cancellation_reason }}</span>
                        </div>
                        @endif
                        @if($followUp->cancellation_details)
                        <div class="col-md-12 mt-2">
                            <strong>{{ __('follow_ups.details') }}:</strong>
                            <p class="mb-0 mt-1">{{ $followUp->cancellation_details }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        @if($followUp->status === 'pending')
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bolt me-2"></i>
                    {{ __('follow_ups.quick_actions') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2 d-md-flex">
                    <form method="POST" action="{{ route('follow-ups.complete', $followUp) }}" class="me-md-2">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check me-1"></i>
                            {{ __('follow_ups.mark_as_completed') }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('follow-ups.cancel', $followUp) }}">
                        @csrf
                        <button type="submit" class="btn btn-danger" 
                                onclick="return confirm('{{ __('follow_ups.confirm_cancel') }}')">
                            <i class="fas fa-times me-1"></i>
                            {{ __('follow_ups.cancel_follow_up') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection