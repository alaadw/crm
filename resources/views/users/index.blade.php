@extends('layouts.app')

@section('title', __('common.users') . ' - CRM Academy')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="h4">
        <i class="fas fa-users-cog me-2"></i>
        {{ __('common.users') }}
      </h1>
      <a href="{{ route('users.create') }}" class="btn btn-primary">
        <i class="fas fa-user-plus me-1"></i>
        {{ __('common.add_user') }}
      </a>
    </div>

    <div class="card">
      <div class="card-body p-0">
        <table class="table mb-0">
          <thead>
            <tr>
              <th>#</th>
              <th>{{ __('common.name') }}</th>
              <th>{{ __('common.email') }}</th>
              <th>{{ __('common.role') }}</th>
              <th>{{ __('common.departments') }}</th>
              <th class="text-end">{{ __('common.actions') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach($users as $u)
              <tr>
                <td>{{ $u->id }}</td>
                <td>{{ $u->name }}</td>
                <td>{{ $u->email }}</td>
                <td><span class="badge bg-secondary">{{ $u->role }}</span></td>
                <td>
                  @php($ids = $u->managed_department_ids)
                  @if(!empty($ids))
                    @foreach($departments->whereIn('id', $ids) as $dept)
                      <span class="badge bg-info me-1">{{ $dept->name }}</span>
                    @endforeach
                  @else
                    <span class="text-muted">{{ __('common.not_set') }}</span>
                  @endif
                </td>
                <td class="text-end">
                  <a href="{{ route('users.edit', $u) }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-edit"></i> {{ __('common.edit') }}
                  </a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="card-footer">
        {{ $users->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
