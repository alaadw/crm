<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
        $user = $this->route('user');
        $userId = $user?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'managed_departments' => ['nullable', 'array'],
            'managed_departments.*' => ['integer', 'exists:categories,id'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'manager_responsible_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(fn ($query) => $query->whereIn('role', ['admin', 'department_manager'])),
                Rule::notIn([$userId]),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'manager_responsible_id.exists' => __('common.invalid_responsible_manager'),
            'manager_responsible_id.not_in' => __('common.cannot_assign_self'),
        ];
    }
}
