<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    private function ensureAdmin(): void
    {
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Unauthorized');
        }
    }

    /**
     * List users (admin only)
     */
    public function index(Request $request): View
    {
        $this->ensureAdmin();

        $users = User::orderBy('name')->paginate(15);
        $departments = Category::query()->where('parent_id', 0)->orderBy('name_ar')->get();

        return view('users.index', compact('users', 'departments'));
    }

    /**
     * Edit a user (admin only)
     */
    public function edit(User $user): View
    {
        $this->ensureAdmin();

        $departments = Category::query()->where('parent_id', 0)->orderBy('name_ar')->get();
        $selected = $user->managed_department_ids; // accessor on model

        return view('users.edit', compact('user', 'departments', 'selected'));
    }

    /**
     * Show create user form (admin only)
     */
    public function create(): View
    {
        $this->ensureAdmin();
        $departments = Category::query()->where('parent_id', 0)->orderBy('name_ar')->get();
        return view('users.create', compact('departments'));
    }

    /**
     * Store new user (admin only)
     */
    public function store(Request $request): RedirectResponse
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:admin,department_manager,sales_rep'],
            'is_active' => ['sometimes', 'boolean'],
            'managed_departments' => ['nullable', 'array'],
            'managed_departments.*' => ['integer', 'exists:categories,id'],
        ]);

        $topLevelIds = Category::query()->where('parent_id', 0)->pluck('id')->all();
        $managed = collect($data['managed_departments'] ?? [])
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values()
            ->intersect($topLevelIds)
            ->all();

        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = bcrypt($data['password']);
        $user->role = $data['role'];
        $user->is_active = (bool) ($data['is_active'] ?? true);
        $user->managed_departments = $managed;
        $user->save();

        return redirect()->route('users.index')->with('success', __('common.saved_successfully'));
    }

    /**
     * Update a user (admin only)
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'managed_departments' => ['nullable', 'array'],
            'managed_departments.*' => ['integer', 'exists:categories,id'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        // Only allow top-level categories
        $topLevelIds = Category::query()->where('parent_id', 0)->pluck('id')->all();
        $selected = collect($data['managed_departments'] ?? [])
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values()
            ->intersect($topLevelIds)
            ->all();

        $user->managed_departments = $selected;
        if (!empty($data['password'])) {
            $user->password = bcrypt($data['password']);
        }
        $user->save();

        return redirect()->route('users.index')
            ->with('success', __('common.saved_successfully'));
    }
}
