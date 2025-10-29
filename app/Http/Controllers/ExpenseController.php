<?php

namespace App\Http\Controllers;

use App\Services\ExpenseService;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\FilterExpensesRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ExpenseController extends Controller
{
    public function __construct(private ExpenseService $service) {}
    private function ensureAdminOrManager(): void
    {
        $u = Auth::user();
        if (!$u || !(method_exists($u, 'isAdmin') && $u->isAdmin()) && !(method_exists($u, 'isDepartmentManager') && $u->isDepartmentManager())) {
            abort(403);
        }
    }

    public function index(FilterExpensesRequest $request): View
    {
        $this->ensureAdminOrManager();

        $query = $this->service->restrictToManagerDepartments(
            $this->service->applyFilters(
                $this->service->baseQuery(),
                $request->validated()
            )
        );

        $totals = $this->service->getTotals($query);
        $expenses = $query->paginate(20)->withQueryString();
        $types = $this->service->getTypes();
        $departments = $this->service->getDepartments();
      
        $chartData = $expenses->groupBy('expense_type_id')->map(function($group){
            return [
                'label' => optional($group->first()->type)->display_name ?? '—',
                'value' => $group->sum('amount'),
            ];
        })->values();

        $totalAmount = $totals['total_amount'] ?? 0;
        return view('expenses.index', compact('expenses','types','departments','totalAmount','chartData'));
    }

    public function create(): View
    {
        $this->ensureAdminOrManager();
        $types = $this->service->getTypes();
        $departments = $this->service->getDepartments();

        return view('expenses.create', compact('types','departments'));
    }

    public function store(StoreExpenseRequest $request): RedirectResponse
    {
        $this->ensureAdminOrManager();
        $this->service->createExpense($request->validated());
        return redirect()->route('expenses.index')->with('success', __('expenses.created'));
    }

    public function exportCsv(FilterExpensesRequest $request)
    {
        $this->ensureAdminOrManager();
        $query = $this->service->restrictToManagerDepartments(
            $this->service->applyFilters(
                $this->service->baseQuery(),
                $request->validated()
            )
        );
        $rows = $query->get();

        $filename = 'expenses_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($rows) {
            $out = fopen('php://output', 'w');
            // UTF-8 BOM for Excel and Arabic
            fwrite($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, ['ID','Type','Amount','Date','Department','Added By','Description']);
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->id,
                    optional($r->type)->display_name,
                    $r->amount,
                    $r->date?->format('Y-m-d'),
                    optional($r->departmentCategory)->name,
                    optional($r->addedBy)->name,
                    $r->description,
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportExcel(FilterExpensesRequest $request)
    {
        $this->ensureAdminOrManager();

        $query = $this->service->restrictToManagerDepartments(
            $this->service->applyFilters(
                $this->service->baseQuery(),
                $request->validated()
            )
        );

        $rows = $query
            ->with(['type', 'departmentCategory', 'addedBy'])
            ->orderBy('date')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(__('expenses.expenses'));

        $headers = [
            __('common.id'),
            __('expenses.date'),
            __('expenses.expense_type'),
            __('common.department'),
            __('expenses.amount'),
            __('common.description'),
            __('expenses.added_by'),
        ];

        foreach ($headers as $index => $heading) {
            $coordinate = Coordinate::stringFromColumnIndex($index + 1) . '1';
            $sheet->setCellValue($coordinate, $heading);
        }

        $rowIndex = 2;
        foreach ($rows as $expense) {
            $sheet->setCellValue('A' . $rowIndex, $expense->id);
            $sheet->setCellValue('B' . $rowIndex, optional($expense->date)->format('Y-m-d'));
            $sheet->setCellValue('C' . $rowIndex, optional($expense->type)->display_name ?? '—');
            $sheet->setCellValue('D' . $rowIndex, optional($expense->departmentCategory)->name ?? '—');
            $sheet->setCellValue('E' . $rowIndex, (float) $expense->amount);
            $sheet->setCellValue('F' . $rowIndex, $expense->description ?? '');
            $sheet->setCellValue('G' . $rowIndex, optional($expense->addedBy)->name ?? '—');
            $rowIndex++;
        }

        $lastRow = max($rowIndex - 1, 2);

        foreach (range(1, count($headers)) as $column) {
            $columnLetter = Coordinate::stringFromColumnIndex($column);
            $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
        }

        $sheet->freezePane('A2');

        $sheet->getStyle('E2:E' . $lastRow)
            ->getNumberFormat()
            ->setFormatCode('#,##0.000');

        $sheet->getStyle('A1:G1')->getFont()->setBold(true);

        $filename = __('expenses.export_filename', [
            'date' => now()->format('Ymd_His'),
        ]);

        if (!Str::endsWith($filename, '.xlsx')) {
            $filename .= '.xlsx';
        }

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function exportPdf(FilterExpensesRequest $request)
    {
        $this->ensureAdminOrManager();

        $query = $this->service->restrictToManagerDepartments(
            $this->service->applyFilters(
                $this->service->baseQuery(),
                $request->validated()
            )
        );

        $rows = $query
            ->with(['type', 'departmentCategory', 'addedBy'])
            ->orderBy('date')
            ->get();

        $totalAmount = $rows->sum('amount');

        $html = view('expenses.print', [
            'rows' => $rows,
            'totalAmount' => $totalAmount,
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

        $filename = __('expenses.export_pdf_filename', [
            'date' => now()->format('Ymd_His'),
        ]);

        if (!Str::endsWith($filename, '.pdf')) {
            $filename .= '.pdf';
        }

        $pdfContent = $mpdf->Output('', Destination::STRING_RETURN);

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    // Chart data endpoints
    public function chartData($period = 'month', $year = null, $month = null)
    {
        $this->ensureAdminOrManager();
        
        $year = $year ?? now()->year;
        $month = $month ?? now()->month;
        
        $query = $this->service->restrictToManagerDepartments(
            $this->service->baseQuery()
        );

        $data = [];

        if ($period === 'day') {
            // Show expenses for each day of the selected month
            $date = \Carbon\Carbon::createFromDate($year, $month, 1);
            $daysInMonth = $date->daysInMonth;
            
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $dayDate = \Carbon\Carbon::createFromDate($year, $month, $day);
                $amount = (clone $query)
                    ->whereDate('date', $dayDate)
                    ->sum('amount');
                $data[] = [
                    'label' => $day,
                    'value' => (float)$amount,
                ];
            }
        } elseif ($period === 'week') {
            // Show expenses for each week of the selected month
            $date = \Carbon\Carbon::createFromDate($year, $month, 1);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();
            
            $week = 1;
            $currentDate = $startOfMonth->copy();
            
            while ($currentDate <= $endOfMonth) {
                $weekEnd = $currentDate->copy()->addDays(6);
                if ($weekEnd > $endOfMonth) $weekEnd = $endOfMonth->copy();
                
                $amount = (clone $query)
                    ->whereBetween('date', [$currentDate->toDateString(), $weekEnd->toDateString()])
                    ->sum('amount');
                
                $data[] = [
                    'label' => __('expenses.week') . ' ' . $week,
                    'value' => (float)$amount,
                ];
                
                $currentDate = $weekEnd->copy()->addDay();
                $week++;
            }
        } else {
            // period === 'month': Show expenses for each month of the selected year
            for ($m = 1; $m <= 12; $m++) {
                $startDate = \Carbon\Carbon::createFromDate($year, $m, 1);
                $endDate = $startDate->copy()->endOfMonth();
                
                $amount = (clone $query)
                    ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
                    ->sum('amount');
                
                $data[] = [
                    'label' => $startDate->format('M'),
                    'value' => (float)$amount,
                ];
            }
        }

        return response()->json([
            'period' => $period,
            'year' => $year,
            'month' => $month,
            'data' => $data,
        ]);
    }

    // Chart data by type
    public function chartDataByType($period = 'month', $year = null, $month = null)
    {
        $this->ensureAdminOrManager();
        
        $year = $year ?? now()->year;
        $month = $month ?? now()->month;
        
        $query = $this->service->restrictToManagerDepartments(
            $this->service->baseQuery()
        );

        if ($period === 'day') {
            $date = \Carbon\Carbon::createFromDate($year, $month, 1);
            $startDate = $date->copy()->startOfMonth();
            $endDate = $date->copy()->endOfMonth();
        } elseif ($period === 'week') {
            $date = \Carbon\Carbon::createFromDate($year, $month, 1);
            $startDate = $date->copy()->startOfMonth();
            $endDate = $date->copy()->endOfMonth();
        } else {
            $startDate = \Carbon\Carbon::createFromDate($year, 1, 1);
            $endDate = \Carbon\Carbon::createFromDate($year, 12, 31);
        }

        $expenses = (clone $query)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->with('type')
            ->get();

        $byType = $expenses->groupBy('expense_type_id')->map(function($group) {
            return [
                'label' => optional($group->first()->type)->display_name ?? '—',
                'value' => (float)$group->sum('amount'),
            ];
        })->values();

        return response()->json([
            'period' => $period,
            'year' => $year,
            'month' => $month,
            'data' => $byType,
        ]);
    }

    // Delete an expense
    public function destroy($id): RedirectResponse
    {
        $this->ensureAdminOrManager();

        $expense = \App\Models\Expense::findOrFail($id);

        // Check if user is admin or manager of this expense's department
        $u = Auth::user();
        if ($u && method_exists($u, 'isDepartmentManager') && $u->isDepartmentManager()) {
            $managedIds = $u->managed_department_ids ?? [];
            if (!in_array((int)$expense->department_category_id, $managedIds)) {
                abort(403, 'Not authorized to delete this expense');
            }
        }

        $expense->delete();

        return redirect()->route('expenses.index')->with('success', __('expenses.deleted'));
    }
}
