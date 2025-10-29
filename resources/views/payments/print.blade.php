<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('payments.payments') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body { font-size: 13px; }
        .table th, .table td { vertical-align: middle; }
        .summary-card { border: 1px solid #dee2e6; border-radius: .5rem; padding: 1rem; margin-bottom: 1.5rem; }
    </style>
</head>
<body>
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 mb-0"><i class="fas fa-hand-holding-usd me-2"></i>{{ __('payments.payments') }}</h2>
        @if($forPrint)
            <button class="btn btn-primary d-print-none" onclick="window.print()"><i class="fas fa-print me-1"></i>{{ __('payments.print_now') }}</button>
        @endif
    </div>

    <div class="summary-card">
        <div class="row g-2">
            <div class="col-md-3"><strong>{{ __('common.from_date') }}:</strong> {{ $filters['start_date'] ?? '—' }}</div>
            <div class="col-md-3"><strong>{{ __('common.to_date') }}:</strong> {{ $filters['end_date'] ?? '—' }}</div>
            <div class="col-md-3"><strong>{{ __('payments.payment_method') }}:</strong> {{ $paymentMethodLabel ?? __('common.all') }}</div>
            <div class="col-md-3"><strong>{{ __('payments.sales_rep') }}:</strong> {{ $salesRepName ?? __('common.all') }}</div>
        </div>
    </div>

    <div class="mb-3">
        <strong>{{ __('payments.total_amount') }}:</strong> {{ number_format($totalAmount, 2) }} {{ __('common.currency_code_jod') }}
    </div>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>{{ __('payments.payment_date') }}</th>
                    <th>{{ __('students.student') }}</th>
                    <th>{{ __('payments.sales_rep') }}</th>
                    <th>{{ __('payments.amount') }}</th>
                    <th>{{ __('payments.currency_code') }}</th>
                    <th>{{ __('payments.amount_jod') }}</th>
                    <th>{{ __('payments.payment_method') }}</th>
                    <th>{{ __('payments.received_by') }}</th>
                    <th>{{ __('payments.class') }}</th>
                    <th>{{ __('payments.notes') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td>{{ optional($payment->payment_date)->format('Y-m-d') }}</td>
                        <td>{{ optional($payment->enrollment?->student)->full_name ?? __('students.not_specified') }}</td>
                        <td>{{ optional($payment->enrollment?->student?->assignedUser)->name ?? '—' }}</td>
                        <td>{{ number_format((float) $payment->amount, 2) }}</td>
                        <td>{{ $payment->currency_code }}</td>
                        <td>{{ number_format((float) $payment->amount_in_jod, 2) }}</td>
                        <td>{{ $payment->payment_method_label }}</td>
                        <td>{{ optional($payment->receivedBy)->name ?? '—' }}</td>
                        <td>{{ optional($payment->enrollment?->courseClass)->class_name ?? '—' }}</td>
                        <td>{{ $payment->notes ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">{{ __('common.no_data') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script src="https://kit.fontawesome.com/a2e0e6ad65.js" crossorigin="anonymous"></script>
</body>
</html>
