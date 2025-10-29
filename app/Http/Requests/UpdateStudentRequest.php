<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->has('assigned_user_id') && $this->input('assigned_user_id') === '') {
            $this->merge(['assigned_user_id' => null]);
        }
    }

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
        $studentId = $this->route('student')->id ?? $this->route('student');
        
        $rules = [
            'full_name' => 'required|string|max:255',
            'full_name_en' => 'nullable|string|max:255',
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('students', 'email')->ignore($studentId)
            ],
            'phone_primary' => 'required|string|max:20',
            'phone_alt' => 'nullable|string|max:20',
            'country_code' => 'nullable|string|max:5',
            'reach_source' => 'required|in:Social Media,University Circular,Purchased Data,Referral,Old Student,Other',
            'department' => 'required|exists:categories,id',
            'preferred_course_id' => 'nullable|exists:courses,id',
            'university' => 'nullable|string|max:255',
            'major' => 'nullable|string|max:255',
            'college' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ];

        $user = $this->user();

        if ($user && method_exists($user, 'isAdmin') && $user->isAdmin()) {
            $rules['assigned_user_id'] = ['nullable', 'integer', 'exists:users,id'];
        } elseif ($user && method_exists($user, 'isDepartmentManager') && $user->isDepartmentManager()) {
            $rules['assigned_user_id'] = [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) use ($user) {
                    $query->where('id', $user->id)
                        ->orWhere('manager_responsible_id', $user->id);
                }),
            ];
        } else {
            $rules['assigned_user_id'] = ['prohibited'];
        }

        return $rules;
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
            'university.max' => __('students.university_max'),
            'major.max' => __('students.major_max'),
            'college.max' => __('students.college_max'),
            'notes.max' => __('students.notes_max'),
            'assigned_user_id.prohibited' => __('students.assigned_user_prohibited'),
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
            'university' => __('students.university'),
            'major' => __('students.major'),
            'college' => __('students.college'),
            'notes' => __('students.notes'),
            'assigned_user_id' => __('students.assigned_user'),
        ];
    }
}
