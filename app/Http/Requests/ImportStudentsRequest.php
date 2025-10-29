<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ImportStudentsRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        foreach (['assigned_user_id', 'department_category_id'] as $field) {
            if ($this->has($field) && $this->input($field) === '') {
                $this->merge([$field => null]);
            }
        }
    }

    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $rules = [
            'file' => ['required', 'file', 'mimes:xlsx,csv,txt'],
        ];

        $user = $this->user();

        if ($user && method_exists($user, 'isAdmin') && $user->isAdmin()) {
            $rules['assigned_user_id'] = ['nullable', 'integer', 'exists:users,id'];
            $rules['department_category_id'] = ['nullable', 'integer', 'exists:categories,id'];
        } elseif ($user && method_exists($user, 'isDepartmentManager') && $user->isDepartmentManager()) {
            $rules['assigned_user_id'] = [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) use ($user) {
                    $query->where('id', $user->id)
                        ->orWhere('manager_responsible_id', $user->id);
                }),
            ];

            $managedIds = $user->managed_department_ids ?? [];
            if (!empty($managedIds)) {
                $rules['department_category_id'] = [
                    'nullable',
                    'integer',
                    Rule::exists('categories', 'id')->where(function ($query) use ($managedIds) {
                        $query->whereIn('id', $managedIds);
                    }),
                ];
            } else {
                $rules['department_category_id'] = ['nullable'];
            }
        } else {
            $rules['assigned_user_id'] = ['prohibited'];
            $rules['department_category_id'] = ['nullable', 'integer', 'exists:categories,id'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'assigned_user_id.prohibited' => __('students.import_assigned_user_prohibited'),
        ];
    }
}
