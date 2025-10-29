<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterPaymentsRequest;
use App\Http\Requests\StorePaymentRequest;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\User;
use App\Services\EnrollmentService;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PaymentController extends Controller
{
    protected $enrollmentService;
    private PaymentService $paymentService;

    public function __construct(EnrollmentService $enrollmentService, PaymentService $paymentService)
    {
        $this->enrollmentService = $enrollmentService;
        $this->paymentService = $paymentService;
    }

    /**
     * Store a new payment for a student's enrollment
     */
    public function storeStudentPayment(StorePaymentRequest $request, Student $student)
    {
        // Verify that the enrollment belongs to the student
        $enrollment = Enrollment::where('id', $request->enrollment_id)
            ->where('student_id', $student->id)
            ->where('is_active', true)
            ->first();

        if (!$enrollment) {
            return redirect()->back()->withErrors([
                'enrollment_id' => __('payments.enrollment_not_found_for_student')
            ]);
        }

        // Skip direct amount vs due comparison here because amount may be in a different currency.
        // EnrollmentService will handle currency conversion and updates safely.

        $result = $this->enrollmentService->handlePaymentAddition(
            $enrollment->id, 
            $request->validated(),
            route('students.show', $student)
        );

        if ($result['success']) {
            return redirect($result['redirect_route'])
                ->with('success', $result['message']);
        } else {
            return redirect()->back()
                ->withInput($result['input'])
                ->withErrors(['error' => $result['error']]);
        }
    }

    /**
     * Store a new payment for an enrollment from class view
     */
    public function storeEnrollmentPayment(StorePaymentRequest $request, Enrollment $enrollment)
    {
        // Verify that the enrollment is active
        if (!$enrollment->is_active) {
            return redirect()->back()->withErrors([
                'enrollment' => __('payments.enrollment_not_active')
            ]);
        }

        // Note: No amount validation here as payment can be in different currency
        
        $result = $this->enrollmentService->handlePaymentAddition(
            $enrollment->id, 
            $request->validated(),
            route('classes.show', $enrollment->course_class_id)
        );

        if ($result['success']) {
            return redirect($result['redirect_route'])
                ->with('success', $result['message']);
        } else {
            return redirect()->back()
                ->withInput($result['input'])
                ->withErrors(['error' => $result['error']]);
        }
    }

    public function index(FilterPaymentsRequest $request): View
    {
        $user = $request->user();
        $filters = $this->defaultedFilters($request->validated());

        $query = $this->paymentService->restrictToUser(
            $this->paymentService->baseQuery(),
            $user
        );

        $filtered = $this->paymentService->applyFilters($query, $filters);
        $totalAmount = $this->paymentService->totalAmount($filtered);
        $payments = (clone $filtered)
            ->orderByDesc('payment_date')
            ->paginate(25)
            ->withQueryString();

        $canFilterSalesRep = $this->canFilterBySalesRep($user);
        $salesReps = $canFilterSalesRep ? $this->paymentService->availableSalesReps() : collect();

        return view('payments.index', [
            'payments' => $payments,
            'totalAmount' => $totalAmount,
            'filters' => $filters,
            'salesReps' => $salesReps,
            'canFilterSalesRep' => $canFilterSalesRep,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePaymentRequest $request)
    {
        $result = $this->enrollmentService->handlePaymentAddition(
            $request->enrollment_id, 
            $request->validated()
        );

        if ($result['success']) {
            return redirect()->back()
                ->with('success', $result['message']);
        } else {
            return redirect()->back()
                ->withInput($result['input'])
                ->withErrors(['error' => $result['error']]);
        }
    }

    public function exportExcel(FilterPaymentsRequest $request)
    {
        $user = $request->user();
        $filters = $this->defaultedFilters($request->validated());
    $export = $this->buildExportDataset($filters, $user);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(__('payments.payments'));

        $headers = [
            __('payments.payment_date'),
            __('students.student'),
            __('payments.sales_rep'),
            __('payments.amount'),
            __('payments.currency_code'),
            __('payments.amount_jod'),
            __('payments.payment_method'),
            __('payments.received_by'),
            __('payments.class'),
            __('payments.notes'),
        ];

        foreach ($headers as $index => $heading) {
            $column = Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($column . '1', $heading);
        }

        $row = 2;
        foreach ($export['payments'] as $payment) {
            $sheet->setCellValue('A' . $row, optional($payment->payment_date)->format('Y-m-d'));
            $sheet->setCellValue('B' . $row, optional($payment->enrollment?->student)->full_name ?? __('students.not_specified'));
            $sheet->setCellValue('C' . $row, optional($payment->enrollment?->student?->assignedUser)->name ?? '—');
            $sheet->setCellValue('D' . $row, (float) $payment->amount);
            $sheet->setCellValue('E' . $row, $payment->currency_code);
            $sheet->setCellValue('F' . $row, (float) $payment->amount_in_jod);
            $sheet->setCellValue('G' . $row, $payment->payment_method_label);
            $sheet->setCellValue('H' . $row, optional($payment->receivedBy)->name ?? '—');
            $sheet->setCellValue('I' . $row, optional($payment->enrollment?->courseClass)->class_name ?? '—');
            $sheet->setCellValue('J' . $row, $payment->notes ?? '—');
            $row++;
        }

        $lastRow = max($row - 1, 2);

        foreach (range(1, count($headers)) as $columnIndex) {
            $column = Coordinate::stringFromColumnIndex($columnIndex);
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $sheet->getStyle('D2:D' . $lastRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('F2:F' . $lastRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->freezePane('A2');

        $filename = __('payments.export_excel_filename', ['date' => now()->format('Ymd_His')]);
        if (!str_ends_with($filename, '.xlsx')) {
            $filename .= '.xlsx';
        }

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function exportPdf(FilterPaymentsRequest $request)
    {
        $user = $request->user();
        $filters = $this->defaultedFilters($request->validated());
        $export = $this->buildExportDataset($filters, $user);

        $html = view('payments.print', [
            'payments' => $export['payments'],
            'totalAmount' => $export['totalAmount'],
            'filters' => $filters,
            'forPrint' => false,
            'salesRepName' => $export['salesRepName'],
            'paymentMethodLabel' => $export['paymentMethodLabel'],
        ])->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'L',
            'default_font' => 'dejavusans',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 15,
            'margin_bottom' => 15,
        ]);

        if (app()->getLocale() === 'ar') {
            $mpdf->SetDirectionality('rtl');
        }

        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;
        $mpdf->WriteHTML($html);

        $filename = __('payments.export_pdf_filename', ['date' => now()->format('Ymd_His')]);
        if (!str_ends_with($filename, '.pdf')) {
            $filename .= '.pdf';
        }

        $pdfContent = $mpdf->Output('', Destination::STRING_RETURN);

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function print(FilterPaymentsRequest $request): View
    {
        $user = $request->user();
        $filters = $this->defaultedFilters($request->validated());
        $export = $this->buildExportDataset($filters, $user);

        return view('payments.print', [
            'payments' => $export['payments'],
            'totalAmount' => $export['totalAmount'],
            'filters' => $filters,
            'forPrint' => true,
            'salesRepName' => $export['salesRepName'],
            'paymentMethodLabel' => $export['paymentMethodLabel'],
        ]);
    }

    public function show($id)
    {
        abort(404);
    }

    public function edit($id)
    {
        abort(404);
    }

    public function update(Request $request, $id)
    {
        abort(404);
    }

    public function destroy($id)
    {
        abort(404);
    }

    /**
     * Return payments for an enrollment (AJAX)
     */
    public function enrollmentPayments(Enrollment $enrollment)
    {
        $enrollment->load(['payments' => function($q) {
            $q->orderByDesc('payment_date');
        }, 'student']);

        $data = [
            'student' => [
                'id' => $enrollment->student?->id,
                'name' => $enrollment->student?->name,
            ],
            'totals' => [
                'total_amount' => (float) $enrollment->total_amount,
                'paid_amount' => (float) $enrollment->paid_amount,
                'due_amount' => (float) $enrollment->due_amount,
            ],
            'payments' => $enrollment->payments->map(function($p) {
                return [
                    'id' => $p->id,
                    'date' => optional($p->payment_date)->format('Y-m-d'),
                    'amount' => $p->amount,
                    'currency' => $p->currency_code,
                    'formatted_amount' => $p->formatted_amount,
                    'method' => $p->payment_method_label,
                    'notes' => $p->notes,
                ];
            }),
        ];

        return response()->json($data);
    }

    public function chartData(Request $request, string $period = 'month', ?int $year = null, ?int $month = null): JsonResponse
    {
        $user = $request->user();
        $filters = array_filter(
            $this->chartFilters($request->query()),
            fn ($value) => $value !== null && $value !== ''
        );

        $normalizedPeriod = $this->normalizePeriod($period);
        $resolvedYear = $year ?? now()->year;
        $resolvedMonth = $month ?? now()->month;

        $query = $this->paymentService->restrictToUser(
            $this->paymentService->baseQuery(),
            $user
        );

        $filtered = $this->paymentService->applyFilters($query, $filters);
        $data = $this->paymentService->chartByPeriod($normalizedPeriod, (int) $resolvedYear, (int) $resolvedMonth, $filtered);

        return response()->json([
            'period' => $normalizedPeriod,
            'year' => (int) $resolvedYear,
            'month' => (int) $resolvedMonth,
            'data' => $data,
        ]);
    }

    public function chartByMethod(Request $request, string $period = 'month', ?int $year = null, ?int $month = null): JsonResponse
    {
        $user = $request->user();
        $filters = array_filter(
            $this->chartFilters($request->query()),
            fn ($value) => $value !== null && $value !== ''
        );

        $normalizedPeriod = $this->normalizePeriod($period);
        $resolvedYear = $year ?? now()->year;
        $resolvedMonth = $month ?? now()->month;

        $query = $this->paymentService->restrictToUser(
            $this->paymentService->baseQuery(),
            $user
        );

        $filtered = $this->paymentService->applyFilters($query, $filters);
        $data = $this->paymentService->chartByMethod($normalizedPeriod, (int) $resolvedYear, (int) $resolvedMonth, $filtered);

        return response()->json([
            'period' => $normalizedPeriod,
            'year' => (int) $resolvedYear,
            'month' => (int) $resolvedMonth,
            'data' => $data,
        ]);
    }

    private function defaultedFilters(array $filters): array
    {
        if (isset($filters['sales_rep_id'])) {
            $filters['sales_rep_id'] = (int) $filters['sales_rep_id'];
        }

        $hasStart = array_key_exists('start_date', $filters) && $filters['start_date'];
        $hasEnd = array_key_exists('end_date', $filters) && $filters['end_date'];

        if ($hasStart || $hasEnd) {
            return $filters;
        }

        $bounds = now();
        $filters['start_date'] = $bounds->copy()->startOfMonth()->toDateString();
        $filters['end_date'] = $bounds->copy()->endOfMonth()->toDateString();

        return $filters;
    }

    private function extractFilters(array $input): array
    {
        $filters = array_filter([
            'start_date' => $input['start_date'] ?? null,
            'end_date' => $input['end_date'] ?? null,
            'payment_method' => $input['payment_method'] ?? null,
            'sales_rep_id' => $input['sales_rep_id'] ?? null,
        ], fn ($value) => $value !== null && $value !== '');

        if (isset($filters['sales_rep_id'])) {
            $filters['sales_rep_id'] = (int) $filters['sales_rep_id'];
        }

        if (isset($filters['payment_method']) && !in_array($filters['payment_method'], ['cash', 'bank_transfer', 'credit_card', 'check', 'zaincash', 'other'], true)) {
            unset($filters['payment_method']);
        }

        return $filters;
    }

    private function chartFilters(array $input): array
    {
        $filters = $this->extractFilters($input);
        unset($filters['start_date'], $filters['end_date']);

        return $filters;
    }

    private function canFilterBySalesRep(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isDepartmentManager()) {
            return true;
        }

        return false;
    }

    private function normalizePeriod(?string $period): string
    {
        $allowed = ['day', 'week', 'month'];
        return in_array($period, $allowed, true) ? $period : 'month';
    }

    private function buildExportDataset(array $filters, ?User $user): array
    {
        $payments = $this->paymentService->collect($filters, $user);
        $totalAmount = $payments->sum(fn ($payment) => (float) $payment->amount_in_jod);
        $salesRepId = $filters['sales_rep_id'] ?? null;
        $salesRepName = $salesRepId ? User::query()->find($salesRepId)?->name : null;
        $paymentMethod = $filters['payment_method'] ?? null;
        $paymentMethodLabel = null;
        if ($paymentMethod) {
            $paymentMethodLabel = $this->paymentService->methodLabel($paymentMethod);
        }

        return compact('payments', 'totalAmount', 'salesRepName', 'paymentMethodLabel');
    }
}
