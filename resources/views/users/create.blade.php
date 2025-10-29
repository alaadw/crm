@extends('layouts.app')

@section('title', __('common.add_user') . ' - CRM Academy')

@section('content')
<div class="row">
  <div class="col-12 col-lg-8">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">
          <i class="fas fa-user-plus me-2"></i>
          {{ __('common.add_user') }}
        </h5>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('users.store') }}">
          @csrf
          <div class="mb-3">
            <label class="form-label">{{ __('common.name') }}</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
          </div>

          <div class="mb-3">
            <label class="form-label">{{ __('common.email') }}</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">{{ __('common.password') }}</label>
                <input type="password" name="password" class="form-control" required>
                @error('password')<div class="text-danger small">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">{{ __('common.confirm_password') }}</label>
                <input type="password" name="password_confirmation" class="form-control" required>
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">{{ __('common.role') }}</label>
            <select name="role" class="form-select" required>
              <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
              <option value="department_manager" {{ old('role') === 'department_manager' ? 'selected' : '' }}>Department Manager</option>
              <option value="sales_rep" {{ old('role') === 'sales_rep' ? 'selected' : '' }}>Sales Rep</option>
            </select>
            @error('role')<div class="text-danger small">{{ $message }}</div>@enderror
          </div>

          <div class="mb-3">
            <label class="form-label">{{ __('common.responsible_manager') }}</label>
            <select name="manager_responsible_id" class="form-select">
              <option value="">{{ __('common.select_option') }}</option>
              @foreach($managers as $manager)
                <option value="{{ $manager->id }}" {{ (string) old('manager_responsible_id') === (string) $manager->id ? 'selected' : '' }}>
                  {{ $manager->name }} ({{ $manager->email }})
                </option>
              @endforeach
            </select>
            @error('manager_responsible_id')<div class="text-danger small">{{ $message }}</div>@enderror
          </div>

          <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">{{ __('common.active') }}</label>
          </div>

          <div class="mb-3">
            <label class="form-label">{{ __('common.managed_departments') }}</label>
            <select class="form-select" name="managed_departments[]" multiple size="8">
              @foreach($departments as $dept)
                <option value="{{ $dept->id }}" {{ collect(old('managed_departments', []))->contains($dept->id) ? 'selected' : '' }}>
                  {{ $dept->name }}
                  @if($dept->name_en && $dept->name_en !== $dept->name)
                    - {{ $dept->name_en }}
                  @endif
                </option>
              @endforeach
            </select>
            <div class="form-text">{{ __('common.select_one_or_more') }}</div>
            @error('managed_departments')<div class="text-danger small">{{ $message }}</div>@enderror
          </div>

          <div class="d-flex gap-2">
            <a class="btn btn-secondary" href="{{ route('users.index') }}">
              <i class="fas fa-arrow-left me-1"></i>{{ __('common.back') }}
            </a>
            <button class="btn btn-primary" type="submit">
              <i class="fas fa-save me-1"></i>{{ __('common.save') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
