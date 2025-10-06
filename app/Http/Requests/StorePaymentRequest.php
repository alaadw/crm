<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'enrollment_id' => 'required|exists:enrollments,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,credit_card,check',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'enrollment_id.required' => __('payments.enrollment_required'),
            'enrollment_id.exists' => __('payments.enrollment_invalid'),
            'amount.required' => __('payments.amount_required'),
            'amount.numeric' => __('payments.amount_numeric'),
            'amount.min' => __('payments.amount_min'),
            'payment_method.required' => __('payments.payment_method_required'),
            'payment_method.in' => __('payments.payment_method_invalid'),
            'payment_date.required' => __('payments.payment_date_required'),
            'payment_date.date' => __('payments.payment_date_invalid'),
            'notes.max' => __('payments.notes_max'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'enrollment_id' => __('payments.enrollment'),
            'amount' => __('payments.amount'),
            'payment_method' => __('payments.payment_method'),
            'payment_date' => __('payments.payment_date'),
            'notes' => __('payments.notes'),
        ];
    }
}
