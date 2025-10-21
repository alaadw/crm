@extends('layouts.app')

@section('title', __('expenses.add_expense') . ' - CRM Academy')

@section('content')
<div class="row">
  <div class="col-12 col-lg-8">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-file-invoice-dollar me-2"></i>{{ __('expenses.add_expense') }}</h5>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('expenses.store') }}">
          @csrf
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">{{ __('expenses.expense_type') }}</label>
                <select name="expense_type_id" class="form-select" required>
                  <option value="">{{ __('common.select_option') }}</option>
                  @foreach($types as $type)
                    <option value="{{ $type->id }}" {{ old('expense_type_id') == $type->id ? 'selected' : '' }}>{{ $type->display_name }}</option>
                  @endforeach
                </select>
                @error('expense_type_id')<div class="text-danger small">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">{{ __('expenses.amount') }}</label>
                <input type="number" step="0.001" name="amount" class="form-control" value="{{ old('amount') }}" required>
                @error('amount')<div class="text-danger small">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">{{ __('expenses.date') }}</label>
                <input type="date" name="date" class="form-control" value="{{ old('date', now()->toDateString()) }}" required>
                @error('date')<div class="text-danger small">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">{{ __('common.department') }}</label>
                <select name="department_category_id" class="form-select" required>
                  <option value="">{{ __('common.select_department') }}</option>
                  @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ old('department_category_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                  @endforeach
                </select>
                @error('department_category_id')<div class="text-danger small">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">{{ __('common.description') }} ({{ __('common.optional') }})</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
            @error('description')<div class="text-danger small">{{ $message }}</div>@enderror
          </div>
          <div class="d-flex gap-2">
            <a href="{{ route('expenses.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>{{ __('common.back') }}</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>{{ __('common.save') }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
