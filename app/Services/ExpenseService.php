<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class ExpenseService
{
    public function getManagedDepartmentIds(): array
    {
        $u = Auth::user();
        if (!$u) return [];
        return method_exists($u, 'getManagedDepartmentIdsAttribute') ? ($u->managed_department_ids ?? []) : [];
    }

    public function baseQuery()
    {
        return Expense::with(['type','departmentCategory','addedBy'])
            ->orderByDesc('date')
            ->orderByDesc('id');
    }

    public function applyFilters($query, array $filters)
    {
        if (!empty($filters['start_date'])) $query->where('date','>=',$filters['start_date']);
        if (!empty($filters['end_date'])) $query->where('date','<=',$filters['end_date']);
        if (!empty($filters['department_category_id'])) $query->where('department_category_id',$filters['department_category_id']);
        if (!empty($filters['expense_type_id'])) $query->where('expense_type_id',$filters['expense_type_id']);
        return $query;
    }

    public function restrictToManagerDepartments($query)
    {
        $u = Auth::user();
        if ($u && method_exists($u,'isDepartmentManager') && $u->isDepartmentManager()) {
            $ids = $this->getManagedDepartmentIds();
            if (!empty($ids)) $query->whereIn('department_category_id',$ids);
            else $query->whereRaw('1=0');
        }
        return $query;
    }

    public function getTotals($query): array
    {
        return [
            'total_amount' => (clone $query)->sum('amount'),
        ];
    }

    public function getTypes()
    {
        return ExpenseType::where('is_active', true)->orderBy('name')->get();
    }

    public function getDepartments()
    {
        $u = Auth::user();
        $q = Category::query()->where('parent_id', 0)->orderBy('name_ar');
        if ($u && method_exists($u,'isDepartmentManager') && $u->isDepartmentManager()) {
            $ids = $this->getManagedDepartmentIds();
            if (!empty($ids)) $q->whereIn('id',$ids); else $q->whereRaw('1=0');
        }
        return $q->get();
    }

    public function getDepartmentsWithVirtual()
    {
        $departments = $this->getDepartments();
        
        // Add virtual departments as collection items
        $other = (object)[
            'id' => 999,
            'name' => __('common.other'),
            'name_ar' => 'أخرى',
            'parent_id' => 0,
        ];
        
        $admin = (object)[
            'id' => 998,
            'name' => __('common.administration'),
            'name_ar' => 'الإدارة',
            'parent_id' => 0,
        ];
        
        return collect([$admin, $other])->merge($departments);
    }

    public function createExpense(array $data)
    {
        $u = Auth::user();
        if ($u && method_exists($u,'isDepartmentManager') && $u->isDepartmentManager()) {
            $ids = $this->getManagedDepartmentIds();
            if (!in_array((int)$data['department_category_id'], $ids)) {
                abort(403);
            }
        }
        $data['added_by_user_id'] = $u?->id;
        return Expense::create($data);
    }
}
