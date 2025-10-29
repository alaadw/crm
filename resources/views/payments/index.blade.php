@extends('layouts.app')

@section('title', __('payments.payments'))

@section('content')
<div class="row">
    <div class="col-12">
        @php
            $exportQuery = array_filter([
                'start_date' => $filters['start_date'] ?? null,
                'end_date' => $filters['end_date'] ?? null,
                'payment_method' => $filters['payment_method'] ?? null,
                'sales_rep_id' => $filters['sales_rep_id'] ?? null,
            ], fn ($value) => $value !== null && $value !== '');
        @endphp
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4"><i class="fas fa-hand-holding-usd me-2"></i>{{ __('payments.payments') }}</h1>
            <div class="btn-group" role="group" aria-label="{{ __('common.export') }}">
                <a
                    class="btn btn-outline-success"
                    href="{{ route('payments.export.excel', $exportQuery) }}"
                >
                    <i class="fas fa-file-excel me-1"></i>{{ __('common.export') }} Excel
                </a>
                <a
                    class="btn btn-outline-danger"
                    href="{{ route('payments.export.pdf', $exportQuery) }}"
                >
                    <i class="fas fa-file-pdf me-1"></i>{{ __('common.export') }} PDF
                </a>
                <a
                    class="btn btn-outline-primary"
                    href="{{ route('payments.print', $exportQuery) }}"
                    target="_blank"
                    rel="noopener"
                >
                    <i class="fas fa-print me-1"></i>{{ __('common.print') }}
                </a>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>{{ __('payments.statistics') }}</h6>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">{{ __('payments.chart_type') }}</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check payment-chart-type" id="chartByTime" name="paymentChartType" value="byTime" checked>
                            <label class="btn btn-outline-primary" for="chartByTime">{{ __('payments.by_time') }}</label>

                            <input type="radio" class="btn-check payment-chart-type" id="chartByMethod" name="paymentChartType" value="byMethod">
                            <label class="btn btn-outline-primary" for="chartByMethod">{{ __('payments.by_method') }}</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('payments.period') }}</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check payment-period" id="periodDay" name="paymentPeriod" value="day">
                            <label class="btn btn-outline-secondary" for="periodDay">{{ __('payments.day') }}</label>

                            <input type="radio" class="btn-check payment-period" id="periodWeek" name="paymentPeriod" value="week">
                            <label class="btn btn-outline-secondary" for="periodWeek">{{ __('payments.week') }}</label>

                            <input type="radio" class="btn-check payment-period" id="periodMonth" name="paymentPeriod" value="month" checked>
                            <label class="btn btn-outline-secondary" for="periodMonth">{{ __('payments.month') }}</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('common.date_range') }}</label>
                        <div class="input-group">
                            <select id="paymentChartYear" class="form-select">
                                @php
                                    $currentYear = now()->year;
                                    for ($y = $currentYear - 5; $y <= $currentYear + 1; $y++) {
                                        $selected = $y === $currentYear ? 'selected' : '';
                                        echo "<option value=\"$y\" $selected>$y</option>";
                                    }
                                @endphp
                            </select>
                            <select id="paymentChartMonth" class="form-select">
                                @php
                                    $monthNames = [
                                        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                                        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                                        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                                    ];
                                    foreach ($monthNames as $number => $name) {
                                        $selected = $number === now()->month ? 'selected' : '';
                                        echo "<option value=\"$number\" $selected>$name</option>";
                                    }
                                @endphp
                            </select>
                        </div>
                    </div>
                </div>
                <div style="position: relative; height: 350px;">
                    <canvas id="paymentsChart"></canvas>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('payments.index') }}" class="row g-2" id="paymentsFilterForm">
                    <div class="col-md-3">
                        <label class="form-label">{{ __('common.from_date') }}</label>
                        <input type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('common.to_date') }}</label>
                        <input type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('payments.payment_method') }}</label>
                        <select name="payment_method" class="form-select">
                            <option value="">{{ __('common.select_option') }}</option>
                            @foreach(['cash','bank_transfer','credit_card','check','zaincash','other'] as $method)
                                <option value="{{ $method }}" {{ ($filters['payment_method'] ?? '') === $method ? 'selected' : '' }}>
                                    {{ __('payments.' . $method) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @if($canFilterSalesRep)
                        <div class="col-md-3">
                            <label class="form-label">{{ __('payments.sales_rep') }}</label>
                            <select name="sales_rep_id" class="form-select">
                                <option value="">{{ __('common.select_option') }} </option>
                                @foreach($salesReps as $rep)
                                    <option value="{{ $rep->id }}" {{ ($filters['sales_rep_id'] ?? null) == $rep->id or request()->query('sales_rep_id') == (string) $rep->id ? 'selected="true"' : '' }}>
                                        {{ $rep->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                        <button class="btn btn-outline-primary" type="submit"><i class="fas fa-filter me-1"></i>{{ __('common.apply_filters') }}</button>
                        <a class="btn btn-outline-secondary" href="{{ route('payments.index') }}"><i class="fas fa-undo me-1"></i>{{ __('common.clear_filters') }}</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-coins fa-2x me-3"></i>
                            <div>
                                <h5 class="card-title mb-0">{{ number_format($totalAmount, 2) }} {{ __('common.currency_code_jod') }}</h5>
                                <small class="text-white-50">{{ __('payments.total_amount') }}</small>
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
                                <th>{{ __('payments.payment_date') }}</th>
                                <th>{{ __('students.student') }}</th>
                                <th>{{ __('payments.sales_rep') }}</th>
                                <th>{{ __('payments.amount') }}</th>
                                <th>{{ __('payments.amount_jod') }}</th>
                                <th>{{ __('payments.payment_method') }}</th>
                                <th>{{ __('payments.received_by') }}</th>
                                <th>{{ __('payments.class') }}</th>
                                <th>{{ __('payments.notes') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                                @php
                                    $enrollment = $payment->enrollment;
                                    $student = $enrollment?->student;
                                    $salesRep = $student?->assignedUser;
                                    $class = $enrollment?->courseClass;
                                @endphp
                                <tr>
                                    <td>{{ optional($payment->payment_date)->format('Y-m-d') }}</td>
                                    <td>
                                        @if($student)
                                            <a href="{{ route('students.edit', $student) }}" class="text-decoration-none">{{ $student->full_name ?? __('students.not_specified') }}</a>
                                        @else
                                            {{ __('students.not_specified') }}
                                        @endif
                                    </td>
                                    <td>{{ $salesRep->name ?? '—' }}</td>
                                    <td>{{ $payment->formatted_amount }}</td>
                                    <td>{{ $payment->formatted_amount_in_jod }}</td>
                                    <td>{{ $payment->payment_method_label }}</td>
                                    <td>{{ $payment->receivedBy->name ?? '—' }}</td>
                                    <td>{{ $class?->class_name ?? '—' }}</td>
                                    <td>{{ $payment->notes ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">{{ __('common.no_data') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                {{ $payments->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('paymentsChart').getContext('2d');
    const chartFilters = @json([
        'sales_rep_id' => $filters['sales_rep_id'] ?? null,
        'payment_method' => $filters['payment_method'] ?? null,
    ]);
    let chartInstance = null;

    const colors = [
        '#1abc9c', '#3498db', '#9b59b6', '#e67e22', '#f1c40f',
        '#e74c3c', '#2ecc71', '#16a085', '#2980b9', '#8e44ad'
    ];

    const buildQueryString = () => {
        const params = new URLSearchParams();
        Object.entries(chartFilters).forEach(([key, value]) => {
            if (!value) return;
            params.append(key, value);
        });
        return params.toString() ? `?${params.toString()}` : '';
    };

    const loadChart = async () => {
        const chartType = document.querySelector('input[name="paymentChartType"]:checked').value;
        const period = document.querySelector('input[name="paymentPeriod"]:checked').value;
        const year = document.getElementById('paymentChartYear').value;
        const month = document.getElementById('paymentChartMonth').value;
        const baseRoute = chartType === 'byTime'
            ? '{{ route("payments.chart-data", ["period" => "PERIOD", "year" => "YEAR", "month" => "MONTH"]) }}'
            : '{{ route("payments.chart-by-method", ["period" => "PERIOD", "year" => "YEAR", "month" => "MONTH"]) }}';

        const url = baseRoute
            .replace('PERIOD', period)
            .replace('YEAR', year)
            .replace('MONTH', month)
            + buildQueryString();

        try {
            const response = await fetch(url);
            const payload = await response.json();
            const labels = payload.data.map(item => item.label);
            const values = payload.data.map(item => item.value);
            const datasetColor = colors.slice(0, values.length);

            const config = {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: chartType === 'byTime' ? '{{ __('payments.total_by_period') }}' : '{{ __('payments.total_by_method') }}',
                        data: values,
                        backgroundColor: datasetColor,
                        borderColor: datasetColor.map(c => c + 'AA'),
                        borderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: '{{ __('payments.amount_jod') }}'
                            }
                        }
                    },
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: {
                            callbacks: {
                                label: (context) => `${context.parsed.y.toFixed(2)} {{ __('common.currency_code_jod') }}`
                            }
                        }
                    }
                }
            };

            if (chartInstance) {
                chartInstance.destroy();
            }

            chartInstance = new Chart(ctx, config);
        } catch (error) {
            console.error('Unable to load payments chart', error);
        }
    };

    document.querySelectorAll('.payment-chart-type, .payment-period').forEach(radio => {
        radio.addEventListener('change', loadChart);
    });

    document.getElementById('paymentChartYear').addEventListener('change', loadChart);
    document.getElementById('paymentChartMonth').addEventListener('change', loadChart);

    loadChart();
});
</script>
@endsection
