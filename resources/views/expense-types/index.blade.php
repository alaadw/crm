@extends('layouts.app')

@section('title', __('expense_types.expense_types') . ' - CRM Academy')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="h4"><i class="fas fa-list me-2"></i>{{ __('expense_types.expense_types') }}</h1>
      <a href="{{ route('expense-types.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> {{ __('expense_types.add_type') }}
      </a>
    </div>

    @if ($message = Session::get('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    @if ($message = Session::get('error'))
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <div class="card">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table mb-0">
            <thead>
              <tr>
                <th>#</th>
                <th>{{ __('common.name') }} (AR)</th>
                <th>{{ __('common.name') }} (EN)</th>
                <th>{{ __('common.active') }}</th>
                <th style="width: 150px;">{{ __('common.actions') }}</th>
              </tr>
            </thead>
            <tbody>
              @forelse($types as $type)
                <tr>
                  <td>{{ $type->id }}</td>
                  <td>{{ $type->name }}</td>
                  <td>{{ $type->name_en }}</td>
                  <td>
                    <span class="badge {{ $type->is_active ? 'bg-success' : 'bg-danger' }}">
                      {{ $type->is_active ? __('common.active') : __('common.inactive') }}
                    </span>
                  </td>
                  <td>
                    <div class="d-flex gap-2">
                      <a href="{{ route('expense-types.edit', $type->id) }}" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i>
                      </a>
                      <form action="{{ route('expense-types.destroy', $type->id) }}" method="POST" class="d-inline delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" title="{{ __('common.delete') }}">
                          <i class="fas fa-trash"></i>
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              @empty
                <tr><td colspan="5" class="text-center text-muted py-4">{{ __('common.no_data') }}</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
      <div class="card-footer">
        {{ $types->links() }}
      </div>
    </div>
  </div>
</div>

<script>
document.querySelectorAll('.delete-form').forEach(form => {
  form.addEventListener('submit', function(e) {
    if (!confirm('{{ __("common.confirm_delete") }}')) {
      e.preventDefault();
    }
  });
});
</script>
@endsection
