@extends('layouts.app')

@section('title', 'Classes & Financials - CRM Academy')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">
                <i class="fas fa-chalkboard-teacher me-2"></i>
                {{ __('classes.classes') }} {{ __('common.and') }} {{ __('common.financial_status') }}
            </h1>
            <div>
                <a href="{{ route('classes.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    {{ __('classes.add_new_class') }}
                </a>
            </div>
        </div>

        <!-- Department Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('classes.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="department" class="form-label">{{ __('common.department') }}</label>
                        <select class="form-select" id="department" name="department">
                            <option value="">{{ __('common.all_departments') }}</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">{{ __('classes.class_status') }}</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">{{ __('common.all_statuses') }}</option>
                            <option value="registration" {{ request('status') == 'registration' ? 'selected' : '' }}>{{ __('classes.registration') }}</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>{{ __('common.in_progress') }}</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('common.completed') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="start_date" class="form-label">{{ __('common.from_date') }}</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label">{{ __('common.to_date') }}</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-filter me-1"></i>
                            {{ __('common.apply_filters') }}
                        </button>
                        <a href="{{ route('classes.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>
                            {{ __('common.clear_filters') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Classes Overview -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">{{ $totalClasses }}</h4>
                                <p class="card-text">{{ __('classes.total_classes') }}</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-chalkboard-teacher fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">{{ $totalStudents }}</h4>
                                <p class="card-text">{{ __('students.total_students') }}</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">{{ number_format($totalPaid, 0) }} JOD</h4>
                                <p class="card-text">{{ __('classes.total_paid') }}</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-money-bill-wave fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">{{ number_format($totalDue, 0) }} JOD</h4>
                                <p class="card-text">{{ __('classes.total_due') }}</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-hourglass-half fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Classes Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('classes.class_list') }}</h5>
            </div>
            <div class="card-body">
                @if($classes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>{{ __('classes.class_name') }}</th>
                                    <th>{{ __('common.department') }}</th>
                                    <th>{{ __('classes.start_date') }}</th>
                                    <th>{{ __('classes.end_date') }}</th>
                                    <th>{{ __('common.status') }}</th>
                                    <th>{{ __('students.students') }}</th>
                                    <th>{{ __('classes.required_amount') }}</th>
                                    <th>{{ __('classes.paid_amount') }}</th>
                                    <th>{{ __('classes.remaining') }}</th>
                                    <th>{{ __('classes.collection_rate') }}</th>
                                    <th>{{ __('common.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($classes as $class)
                                <tr>
                                    <td>
                                        <a href="{{ route('classes.show', $class) }}" class="text-decoration-none">
                                            <strong>{{ $class->class_name }}</strong>
                                        </a>
                                        <br><small class="text-muted">{{ $class->class_code }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $class->category->name }}</span>
                                    </td>
                                    <td>{{ $class->start_date->format('Y-m-d') }}</td>
                                    <td>{{ $class->end_date->format('Y-m-d') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $class->status_color }}">{{ $class->status_label }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $class->total_enrolled_students }}</span>
                                    </td>
                                    <td>{{ number_format($class->total_required_amount, 0) }} JOD</td>
                                    <td>{{ number_format($class->total_paid_amount, 0) }} JOD</td>
                                    <td>{{ number_format($class->total_due_amount, 0) }} JOD</td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: {{ $class->collection_rate }}%;" 
                                                 aria-valuenow="{{ $class->collection_rate }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                                {{ number_format($class->collection_rate, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('classes.show', $class) }}" 
                                               class="btn btn-sm btn-outline-primary" title="{{ __('classes.view_details') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('classes.edit', $class) }}" 
                                               class="btn btn-sm btn-outline-warning" title="{{ __('common.edit') }}">
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
                    <div class="d-flex justify-content-center">
                        {{ $classes->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-chalkboard-teacher fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">{{ __('classes.no_classes') }}</h5>
                        <p class="text-muted">{{ __('classes.add_new_class_to_start') }}</p>
                        <a href="{{ route('classes.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>
                            {{ __('classes.add_new_class') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection