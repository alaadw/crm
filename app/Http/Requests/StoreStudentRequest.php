<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
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
            'full_name' => 'required|string|max:255',
            'full_name_en' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:students,email',
            'phone_primary' => 'required|string|max:20',
            'phone_alt' => 'nullable|string|max:20',
            'country_code' => 'nullable|string|max:5',
            'reach_source' => 'required|in:Social Media,University Circular,Purchased Data,Referral,Old Student,Other',
            'department' => 'required|exists:categories,id',
            'preferred_course_id' => 'nullable|exists:courses,id',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'full_name.required' => __('students.full_name_required'),
            'full_name.max' => __('students.full_name_max'),
            'full_name_en.max' => __('students.full_name_en_max'),
            'email.email' => __('students.email_invalid'),
            'email.max' => __('students.email_max'),
            'email.unique' => __('students.email_unique'),
            'phone_primary.required' => __('students.phone_primary_required'),
            'phone_primary.max' => __('students.phone_primary_max'),
            'phone_alt.max' => __('students.phone_alt_max'),
            'country_code.max' => __('students.country_code_max'),
            'reach_source.required' => __('students.reach_source_required'),
            'reach_source.in' => __('students.reach_source_invalid'),
            'department.required' => __('students.department_required'),
            'department.exists' => __('students.department_invalid'),
            'preferred_course_id.exists' => __('students.preferred_course_invalid'),
            'notes.max' => __('students.notes_max'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'full_name' => __('students.full_name'),
            'full_name_en' => __('students.full_name_en'),
            'email' => __('students.email'),
            'phone_primary' => __('students.primary_phone'),
            'phone_alt' => __('students.alternative_phone'),
            'country_code' => __('students.country_code'),
            'reach_source' => __('students.how_did_they_reach_us'),
            'department' => __('students.department'),
            'preferred_course_id' => __('students.preferred_course'),
            'notes' => __('students.notes'),
        ];
    }
}
