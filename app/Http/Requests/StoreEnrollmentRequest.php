<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEnrollmentRequest extends FormRequest
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
            'student_id' => 'required|exists:students,id',
            'department' => 'nullable|exists:categories,id',
            'course_class_id' => 'required|exists:course_classes,id',
            'total_amount' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'enrollment_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
            'payment_method' => 'nullable|in:cash,bank_transfer,credit_card,check',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'student_id.required' => __('enrollments.student_required'),
            'student_id.exists' => __('enrollments.student_invalid'),
            'department.exists' => __('enrollments.department_invalid'),
            'course_class_id.required' => __('enrollments.course_class_required'),
            'course_class_id.exists' => __('enrollments.course_class_invalid'),
            'total_amount.required' => __('enrollments.total_amount_required'),
            'total_amount.numeric' => __('enrollments.total_amount_numeric'),
            'total_amount.min' => __('enrollments.total_amount_min'),
            'paid_amount.numeric' => __('enrollments.paid_amount_numeric'),
            'paid_amount.min' => __('enrollments.paid_amount_min'),
            'enrollment_date.required' => __('enrollments.enrollment_date_required'),
            'enrollment_date.date' => __('enrollments.enrollment_date_invalid'),
            'notes.max' => __('enrollments.notes_max'),
            'payment_method.in' => __('enrollments.payment_method_invalid'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'student_id' => __('enrollments.student'),
            'department' => __('enrollments.department'),
            'course_class_id' => __('enrollments.course_class'),
            'total_amount' => __('enrollments.total_amount'),
            'paid_amount' => __('enrollments.paid_amount'),
            'enrollment_date' => __('enrollments.enrollment_date'),
            'notes' => __('enrollments.notes'),
            'payment_method' => __('enrollments.payment_method'),
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $paidAmount = $this->input('paid_amount', 0);
            $totalAmount = $this->input('total_amount', 0);
            
            if ($paidAmount > $totalAmount) {
                $validator->errors()->add('paid_amount', __('enrollments.paid_amount_exceeds_total'));
            }
        });
    }
}
