<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCourseClassRequest extends FormRequest
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
        $classId = $this->route('class')->id ?? $this->route('class');
        
        return [
            'class_name' => 'required|string|max:255',
            'class_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('course_classes')->ignore($classId)
            ],
            'course_id' => 'required|exists:courses,id',
            'category_id' => 'required|exists:categories,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:registration,in_progress,completed',
            'class_fee' => 'required|numeric|min:0',
            'default_price' => 'sometimes|numeric|min:0', // Added for mapping
            'description' => 'nullable|string',
            'max_students' => 'nullable|integer|min:1',
            'instructor_name' => 'nullable|string|max:255',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Map class_fee to default_price for database compatibility
        if ($this->has('class_fee')) {
            $this->merge([
                'default_price' => $this->class_fee,
            ]);
        }
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'class_name.required' => __('classes.class_name_required'),
            'class_name.max' => __('classes.class_name_max'),
            'class_code.required' => __('classes.class_code_required'),
            'class_code.max' => __('classes.class_code_max'),
            'class_code.unique' => __('classes.class_code_unique'),
            'course_id.required' => __('classes.course_required'),
            'course_id.exists' => __('classes.course_invalid'),
            'category_id.required' => __('classes.category_required'),
            'category_id.exists' => __('classes.category_invalid'),
            'start_date.required' => __('classes.start_date_required'),
            'start_date.date' => __('classes.start_date_invalid'),
            'end_date.required' => __('classes.end_date_required'),
            'end_date.date' => __('classes.end_date_invalid'),
            'end_date.after' => __('classes.end_date_after_start'),
            'status.required' => __('classes.status_required'),
            'status.in' => __('classes.status_invalid'),
            'class_fee.numeric' => __('classes.class_fee_numeric'),
            'class_fee.min' => __('classes.class_fee_min'),
            'max_students.integer' => __('classes.max_students_integer'),
            'max_students.min' => __('classes.max_students_min'),
            'instructor_name.max' => __('classes.instructor_name_max'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'class_name' => __('classes.class_name'),
            'class_code' => __('classes.class_code'),
            'course_id' => __('classes.course'),
            'category_id' => __('classes.category'),
            'start_date' => __('classes.start_date'),
            'end_date' => __('classes.end_date'),
            'status' => __('classes.status'),
            'class_fee' => __('classes.class_fee'),
            'description' => __('classes.description'),
            'max_students' => __('classes.max_students'),
            'instructor_name' => __('classes.instructor_name'),
        ];
    }
}
