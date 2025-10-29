<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Models\Category;
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
    public function index(): View
    {
        $this->ensureAdmin();

        $users = User::with('responsibleManager')->orderBy('name')->paginate(15);
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
        $managers = $this->managerOptions($user->id);

        return view('users.edit', compact('user', 'departments', 'selected', 'managers'));
    }

    /**
     * Show create user form (admin only)
     */
    public function create(): View
    {
        $this->ensureAdmin();
        $departments = Category::query()->where('parent_id', 0)->orderBy('name_ar')->get();
        $managers = $this->managerOptions();

        return view('users.create', compact('departments', 'managers'));
    }

    /**
     * Store new user (admin only)
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->ensureAdmin();

        $data = $request->validated();

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
        $user->manager_responsible_id = $data['manager_responsible_id'] ?? null;
        $user->save();

        return redirect()->route('users.index')->with('success', __('common.saved_successfully'));
    }

    /**
     * Update a user (admin only)
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->ensureAdmin();

        $data = $request->validated();

        // Only allow top-level categories
        $topLevelIds = Category::query()->where('parent_id', 0)->pluck('id')->all();
        $selected = collect($data['managed_departments'] ?? [])
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values()
            ->intersect($topLevelIds)
            ->all();

        $user->managed_departments = $selected;
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->manager_responsible_id = $data['manager_responsible_id'] ?? null;
        if (!empty($data['password'])) {
            $user->password = bcrypt($data['password']);
        }
        $user->save();

        return redirect()->route('users.index')
            ->with('success', __('common.saved_successfully'));
    }

    private function managerOptions(?int $excludeId = null)
    {
        $query = User::query()
            ->whereIn('role', ['admin', 'department_manager'])
            ->orderBy('name');

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->get();
    }
}
