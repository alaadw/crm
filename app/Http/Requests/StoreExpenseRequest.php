<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'expense_type_id' => ['required','exists:expense_types,id'],
            'amount' => ['required','numeric','min:0'],
            'date' => ['required','date'],
            'department_category_id' => ['required','exists:categories,id'],
            'description' => ['nullable','string','max:2000'],
        ];
    }
}
