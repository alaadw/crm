<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterPaymentsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'payment_method' => ['nullable', 'string', 'max:50', 'in:cash,bank_transfer,credit_card,check,zaincash,other'],
            'sales_rep_id' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }
}
