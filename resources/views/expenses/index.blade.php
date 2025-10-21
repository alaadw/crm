@extends('layouts.app')

@section('title', __('expenses.expenses') . ' - CRM Academy')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="h4"><i class="fas fa-file-invoice-dollar me-2"></i>{{ __('expenses.expenses') }}</h1>
      <div class="d-flex gap-2">
  <a href="{{ route('expenses.export.excel', request()->query()) }}" class="btn btn-outline-success"><i class="fas fa-file-excel me-1"></i> {{ __('expenses.export_excel') }}</a>
  <a href="{{ route('expenses.export.csv', request()->query()) }}" class="btn btn-outline-secondary"><i class="fas fa-file-csv me-1"></i> {{ __('expenses.export_csv') }}</a>
  <a href="{{ route('expenses.export.pdf', request()->query()) }}" class="btn btn-outline-danger"><i class="fas fa-file-pdf me-1"></i> {{ __('expenses.export_pdf') }}</a>
        <a href="{{ route('expenses.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i> {{ __('expenses.add_expense') }}</a>
      </div>
    </div>

    <!-- Chart Controls -->
    <div class="card mb-3">
      <div class="card-header">
        <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>{{ __('expenses.statistics') }}</h6>
      </div>
      <div class="card-body">
        <div class="row mb-3">
          <div class="col-md-4">
            <label class="form-label">{{ __('expenses.chart_type') }}</label>
            <div class="btn-group w-100" role="group">
              <input type="radio" class="btn-check chart-type-radio" id="chartByTime" name="chartType" value="byTime" checked>
              <label class="btn btn-outline-primary" for="chartByTime">{{ __('expenses.by_time') }}</label>

              <input type="radio" class="btn-check chart-type-radio" id="chartByType" name="chartType" value="byType">
              <label class="btn btn-outline-primary" for="chartByType">{{ __('expenses.by_type') }}</label>
            </div>
          </div>
          <div class="col-md-4">
            <label class="form-label">{{ __('expenses.period') }}</label>
            <div class="btn-group w-100" role="group">
              <input type="radio" class="btn-check period-radio" id="periodDay" name="period" value="day">
              <label class="btn btn-outline-secondary" for="periodDay">{{ __('expenses.day') }}</label>

              <input type="radio" class="btn-check period-radio" id="periodWeek" name="period" value="week">
              <label class="btn btn-outline-secondary" for="periodWeek">{{ __('expenses.week') }}</label>

              <input type="radio" class="btn-check period-radio" id="periodMonth" name="period" value="month" checked>
              <label class="btn btn-outline-secondary" for="periodMonth">{{ __('expenses.month') }}</label>
            </div>
          </div>
          <div class="col-md-4">
            <label class="form-label">{{ __('common.date_range') }}</label>
            <div class="input-group">
              <select id="chartYear" class="form-select">
                @php
                  $currentYear = now()->year;
                  for ($y = $currentYear - 5; $y <= $currentYear + 1; $y++) {
                    $selected = $y == $currentYear ? 'selected' : '';
                    echo "<option value=\"$y\" $selected>$y</option>";
                  }
                @endphp
              </select>
              <select id="chartMonth" class="form-select">
                @php
                  $months = [
                    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                  ];
                  foreach ($months as $m => $name) {
                    $selected = $m == now()->month ? 'selected' : '';
                    echo "<option value=\"$m\" $selected>$name</option>";
                  }
                @endphp
              </select>
            </div>
          </div>
        </div>
        <div style="position: relative; height: 350px;">
          <canvas id="expensesChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
      <div class="card-body">
        <form method="GET" action="{{ route('expenses.index') }}" class="row g-2">
          <div class="col-md-3">
            <label class="form-label">{{ __('common.from_date') }}</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
          </div>
          <div class="col-md-3">
            <label class="form-label">{{ __('common.to_date') }}</label>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
          </div>
          <div class="col-md-3">
            <label class="form-label">{{ __('common.department') }}</label>
            <select name="department_category_id" class="form-select">
              <option value="">{{ __('common.select_department') }}</option>
              @foreach($departments as $dept)
                <option value="{{ $dept->id }}" {{ request('department_category_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">{{ __('expenses.expense_type') }}</label>
            <select name="expense_type_id" class="form-select">
              <option value="">{{ __('common.select_option') }}</option>
              @foreach($types as $type)
                <option value="{{ $type->id }}" {{ request('expense_type_id') == $type->id ? 'selected' : '' }}>{{ $type->display_name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-12 d-flex justify-content-end gap-2 mt-2">
            <button class="btn btn-outline-primary" type="submit"><i class="fas fa-filter me-1"></i>{{ __('common.apply_filters') }}</button>
            <a class="btn btn-outline-secondary" href="{{ route('expenses.index') }}"><i class="fas fa-undo me-1"></i>{{ __('common.clear_filters') }}</a>
          </div>
        </form>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-3">
        <div class="card text-white bg-primary">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <i class="fas fa-coins fa-2x me-3"></i>
              <div>
                <h5 class="card-title mb-0">{{ number_format($totalAmount, 3) }} {{ __('common.currency') }}</h5>
                <small class="text-white-50">{{ __('expenses.total_expenses') }}</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table mb-0">
            <thead>
              <tr>
                <th>#</th>
                <th>{{ __('expenses.date') }}</th>
                <th>{{ __('expenses.expense_type') }}</th>
                <th>{{ __('common.department') }}</th>
                <th>{{ __('expenses.amount') }}</th>
                <th>{{ __('common.description') }}</th>
                <th>{{ __('expenses.added_by') }}</th>
                <th style="width: 80px;">{{ __('common.actions') }}</th>
              </tr>
            </thead>
            <tbody>
              @forelse($expenses as $e)
                <tr>
                  <td>{{ $e->id }}</td>
                  <td>{{ $e->date->format('Y-m-d') }}</td>
                  <td>{{ optional($e->type)->display_name ?? '—' }}</td>
                  <td>{{ $e->departmentCategory->name ?? '—' }}</td>
                  <td>{{ number_format($e->amount, 3) }} {{ __('common.currency') }}</td>
                  <td>{{ $e->description }}</td>
                  <td>{{ $e->addedBy->name ?? '—' }}</td>
                  <td>
                    <form action="{{ route('expenses.destroy', $e->id) }}" method="POST" class="d-inline delete-form">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-danger" title="{{ __('common.delete') }}">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr><td colspan="8" class="text-center text-muted py-4">{{ __('common.no_data') }}</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
      <div class="card-footer">
        {{ $expenses->links() }}
      </div>
    </div>
  </div>
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  let chartInstance = null;
  const chartCanvas = document.getElementById('expensesChart');
  const ctx = chartCanvas.getContext('2d');

  // Helper function to get chart colors
  function getChartColors(count) {
    const colors = [
      '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
      '#FF9F40', '#FF6384', '#C9CBCF', '#4BC0C0', '#FF6384'
    ];
    return colors.slice(0, count).concat(colors.slice(0, Math.max(0, count - colors.length)));
  }

  // Fetch and render chart
  async function loadChart() {
    const chartType = document.querySelector('input[name="chartType"]:checked').value;
    const period = document.querySelector('input[name="period"]:checked').value;
    const year = document.getElementById('chartYear').value;
    const month = document.getElementById('chartMonth').value;

    try {
      const endpoint = chartType === 'byTime' ? 'expenses.chart-data' : 'expenses.chart-by-type';
      const baseUrl = chartType === 'byTime'
        ? '{{ route("expenses.chart-data", ["period" => "PERIOD", "year" => "YEAR", "month" => "MONTH"]) }}'
        : '{{ route("expenses.chart-by-type", ["period" => "PERIOD", "year" => "YEAR", "month" => "MONTH"]) }}';
      
      const url = baseUrl
        .replace('PERIOD', period)
        .replace('YEAR', year)
        .replace('MONTH', month);

      const response = await fetch(url);
      const data = await response.json();

      // Prepare chart data
      const labels = data.data.map(item => item.label);
      const values = data.data.map(item => item.value);
      const backgroundColor = getChartColors(values.length);

      const chartData = {
        labels: labels,
        datasets: [{
          label: chartType === 'byTime' ? '{{ __("expenses.total_by_period") }}' : '{{ __("expenses.total_by_type") }}',
          data: values,
          backgroundColor: backgroundColor,
          borderColor: backgroundColor.map(c => c.replace('FF', '80')),
          borderWidth: 2,
          fill: false,
          tension: 0.1,
        }]
      };

      const chartConfig = {
        type: 'bar',
        data: chartData,
        options: {
          responsive: true,
          maintainAspectRatio: false,
          animation: {
            duration: 750,
            easing: 'easeInOutQuart'
          },
          plugins: {
            legend: {
              display: true,
              position: 'top',
            },
            tooltip: {
              backgroundColor: 'rgba(0,0,0,0.8)',
              padding: 12,
              titleFont: { size: 14 },
              bodyFont: { size: 13 },
              callbacks: {
                label: function(context) {
                  return context.parsed.y.toFixed(3) + ' {{ __("common.currency") }}';
                }
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              title: {
                display: true,
                text: '{{ __("expenses.amount") }}'
              }
            }
          }
        }
      };

      // Destroy existing chart instance
      if (chartInstance) {
        chartInstance.destroy();
      }

      // Create new chart
      chartInstance = new Chart(ctx, chartConfig);
    } catch (error) {
      console.error('Error loading chart:', error);
    }
  }

  // Event listeners
  document.querySelectorAll('.chart-type-radio').forEach(radio => {
    radio.addEventListener('change', loadChart);
  });

  document.querySelectorAll('.period-radio').forEach(radio => {
    radio.addEventListener('change', loadChart);
  });

  document.getElementById('chartYear').addEventListener('change', loadChart);
  document.getElementById('chartMonth').addEventListener('change', loadChart);

  // Load initial chart
  loadChart();
});

// Delete confirmation
document.querySelectorAll('.delete-form').forEach(form => {
  form.addEventListener('submit', function(e) {
    if (!confirm('{{ __("common.confirm_delete") }}')) {
      e.preventDefault();
    }
  });
});
</script>
@endsection
