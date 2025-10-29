<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkAssignStudentsRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->has('assigned_user_id') && $this->input('assigned_user_id') === '') {
            $this->merge(['assigned_user_id' => null]);
        }
    }

    public function authorize(): bool
    {
        $user = $this->user();
        if (!$user) {
            return false;
        }

        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return true;
        }

        return method_exists($user, 'isDepartmentManager') && $user->isDepartmentManager();
    }

    public function rules(): array
    {
        $rules = [
            'student_ids' => ['required', 'array'],
            'student_ids.*' => ['integer', 'exists:students,id'],
        ];

        $user = $this->user();

        if ($user && method_exists($user, 'isAdmin') && $user->isAdmin()) {
            $rules['assigned_user_id'] = ['required', 'integer', 'exists:users,id'];
        } elseif ($user && method_exists($user, 'isDepartmentManager') && $user->isDepartmentManager()) {
            $rules['assigned_user_id'] = [
                'required',
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

    public function messages(): array
    {
        return [
            'student_ids.required' => __('students.bulk_assign_students_required'),
            'student_ids.array' => __('students.bulk_assign_students_array'),
            'student_ids.*.exists' => __('students.bulk_assign_students_invalid'),
            'assigned_user_id.required' => __('students.assigned_user_required'),
            'assigned_user_id.integer' => __('students.assigned_user_invalid'),
            'assigned_user_id.exists' => __('students.assigned_user_invalid'),
            'assigned_user_id.prohibited' => __('students.assigned_user_prohibited'),
        ];
    }

    public function attributes(): array
    {
        return [
            'student_ids' => __('students.students'),
            'assigned_user_id' => __('students.assigned_user'),
        ];
    }
}
