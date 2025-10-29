<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->has('manager_responsible_id') && $this->input('manager_responsible_id') === '') {
            $this->merge(['manager_responsible_id' => null]);
        }
    }

    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:admin,department_manager,sales_rep'],
            'is_active' => ['sometimes', 'boolean'],
            'managed_departments' => ['nullable', 'array'],
            'managed_departments.*' => ['integer', 'exists:categories,id'],
            'manager_responsible_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(fn ($query) => $query->whereIn('role', ['admin', 'department_manager'])),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'manager_responsible_id.exists' => __('common.invalid_responsible_manager'),
        ];
    }

}
