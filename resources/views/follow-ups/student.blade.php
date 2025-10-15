@extends('layouts.app')

@section('title', __('follow_ups.follow_ups_for') . ' ' . $student->full_name . ' - CRM Academy')

@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">
                <i class="fas fa-comments me-2"></i>
                {{ __('follow_ups.follow_ups_for') }} {{ $student->full_name }}
            </h1>
            <div>
                <a href="{{ route('students.show', $student) }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i>
                    {{ __('follow_ups.back_to_student') }}
                </a>
                <a href="{{ route('follow-ups.create', ['student_id' => $student->id]) }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    {{ __('follow_ups.add_follow_up') }}
                </a>
            </div>
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
                            @if($student->email)
                                • <i class="fas fa-envelope me-1"></i>
                                {{ $student->email }}
                            @endif
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

        <!-- Follow-ups List -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>
                    {{ __('follow_ups.all_follow_ups') }} ({{ $followUps->total() }})
                </h5>
            </div>
            <div class="card-body">
                @if($followUps->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('follow_ups.date') }}</th>
                                    <th>{{ __('follow_ups.type') }}</th>
                                    <th>{{ __('follow_ups.purpose') }}</th>
                                    <th>{{ __('follow_ups.method') }}</th>
                                    <th>{{ __('follow_ups.priority') }}</th>
                                    <th>{{ __('common.status') }}</th>
                                    <th>{{ __('follow_ups.assigned_to') }}</th>
                                    <th>{{ __('common.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($followUps as $followUp)
                                <tr>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-medium">{{ $followUp->scheduled_date->format('M j, Y') }}</span>
                                            <small class="text-muted">{{ $followUp->scheduled_date->format('g:i A') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $followUp->type)) }}</span>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 200px;" title="{{ $followUp->purpose }}">
                                            {{ $followUp->purpose }}
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            @switch($followUp->contact_method)
                                                @case('phone')
                                                    <i class="fas fa-phone me-1"></i>{{ __('follow_ups.phone') }}
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
                                        </small>
                                    </td>
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
                                    <td>
                                        @switch($followUp->status)
                                            @case('completed')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check me-1"></i>{{ __('follow_ups.completed') }}
                                                </span>
                                                @break
                                            @case('cancelled')
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times me-1"></i>{{ __('follow_ups.cancelled') }}
                                                </span>
                                                @break
                                            @case('pending')
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-clock me-1"></i>{{ __('follow_ups.pending') }}
                                                </span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ ucfirst($followUp->status) }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $followUp->user->name ?? __('common.system') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('follow-ups.show', $followUp) }}" 
                                               class="btn btn-outline-primary" title="{{ __('common.view') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('follow-ups.edit', $followUp) }}" 
                                               class="btn btn-outline-warning" title="{{ __('common.edit') }}">
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
                    <div class="d-flex justify-content-center mt-3">
                        {{ $followUps->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">{{ __('follow_ups.no_follow_ups_found') }}</h5>
                        <p class="text-muted">
                            {{ __('follow_ups.start_tracking_interactions') }}
                        </p>
                        <a href="{{ route('follow-ups.create', ['student_id' => $student->id]) }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>
                            {{ __('follow_ups.add_follow_up') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection