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
     * Update a user (admin only)
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'managed_departments' => ['nullable', 'array'],
            'managed_departments.*' => ['integer', 'exists:categories,id'],
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
        $user->save();

        return redirect()->route('users.index')
            ->with('success', __('common.saved_successfully'));
    }
}
