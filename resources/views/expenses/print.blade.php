<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale()==='ar' ? 'rtl' : 'ltr' }}">
@php($isRtl = app()->getLocale()==='ar')
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ __('expenses.expenses') }}</title>
  <style>
    body {
      font-family: 'DejaVu Sans', sans-serif;
      font-size: 12px;
      padding: 20px;
      color: #222;
    }
    h3 {
      margin: 0 0 12px 0;
      font-size: 18px;
    }
    .summary {
      margin-bottom: 16px;
      font-size: 13px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      border: 1px solid #d1d5db;
      padding: 8px;
      vertical-align: middle;
      text-align: {{ $isRtl ? 'right' : 'left' }};
    }
    th {
      background: #f3f4f6;
      font-weight: bold;
    }
    tbody tr:nth-child(even) {
      background: #fafafa;
    }
  </style>
</head>
<body>
  <h3>{{ __('expenses.expenses') }}</h3>
  <p class="summary">{{ __('expenses.total_expenses') }}:
    <strong>{{ number_format($totalAmount, 3) }} {{ __('common.currency') }}</strong>
  </p>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>{{ __('expenses.date') }}</th>
        <th>{{ __('expenses.expense_type') }}</th>
        <th>{{ __('common.department') }}</th>
        <th>{{ __('expenses.amount') }}</th>
        <th>{{ __('common.description') }}</th>
        <th>{{ __('expenses.added_by') }}</th>
      </tr>
    </thead>
    <tbody>
      @forelse($rows as $e)
      <tr>
        <td>{{ $e->id }}</td>
        <td>{{ optional($e->date)->format('Y-m-d') }}</td>
        <td>{{ optional($e->type)->display_name ?? '—' }}</td>
        <td>{{ optional($e->departmentCategory)->name ?? '—' }}</td>
        <td>{{ number_format($e->amount, 3) }} {{ __('common.currency') }}</td>
        <td>{{ $e->description }}</td>
        <td>{{ optional($e->addedBy)->name ?? '—' }}</td>
      </tr>
      @empty
      <tr>
        <td colspan="7" style="text-align: center; padding: 16px;">{{ __('common.no_data') }}</td>
      </tr>
      @endforelse
    </tbody>
  </table>
</body>
</html>