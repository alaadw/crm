@extends('layouts.app')

@section('title', __('expense_types.edit_type') . ' - CRM Academy')

@section('content')
<div class="row">
  <div class="col-12 col-lg-8">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-edit me-2"></i>{{ __('expense_types.edit_type') }}</h5>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('expense-types.update', $type->id) }}">
          @csrf
          @method('PUT')
          <div class="mb-3">
            <label class="form-label">{{ __('common.name') }} (Arabic)</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $type->name) }}" required>
            @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">{{ __('common.name') }} (English)</label>
            <input type="text" name="name_en" class="form-control @error('name_en') is-invalid @enderror" value="{{ old('name_en', $type->name_en) }}" required>
            @error('name_en')<div class="text-danger small">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <div class="form-check">
              <input type="checkbox" name="is_active" class="form-check-input" id="isActive" value="1" {{ old('is_active', $type->is_active) ? 'checked' : '' }}>
              <label class="form-check-label" for="isActive">
                {{ __('common.active') }}
              </label>
            </div>
          </div>
          <div class="d-flex gap-2">
            <a href="{{ route('expense-types.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>{{ __('common.back') }}</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>{{ __('common.save') }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
