<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterExpensesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'start_date' => ['nullable','date'],
            'end_date' => ['nullable','date','after_or_equal:start_date'],
            'department_category_id' => ['nullable','integer','exists:categories,id'],
            'expense_type_id' => ['nullable','integer','exists:expense_types,id'],
        ];
    }
}
