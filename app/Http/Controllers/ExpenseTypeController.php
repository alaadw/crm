<?php

namespace App\Http\Controllers;

use App\Models\ExpenseType;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ExpenseTypeController extends Controller
{
    private function ensureAdmin(): void
    {
        $u = \Illuminate\Support\Facades\Auth::user();
        if (!$u || !(method_exists($u, 'isAdmin') && $u->isAdmin())) {
            abort(403, 'Only admins can manage expense types');
        }
    }

    public function index(): View
    {
        $this->ensureAdmin();
        $types = ExpenseType::orderBy('name')->paginate(20);
        return view('expense-types.index', compact('types'));
    }

    public function create(): View
    {
        $this->ensureAdmin();
        return view('expense-types.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        ExpenseType::create($validated);

        return redirect()->route('expense-types.index')->with('success', __('expense_types.created'));
    }

    public function edit(ExpenseType $type): View
    {
        $this->ensureAdmin();
        return view('expense-types.edit', compact('type'));
    }

    public function update(Request $request, ExpenseType $type): RedirectResponse
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $type->update($validated);

        return redirect()->route('expense-types.index')->with('success', __('expense_types.updated'));
    }

    public function destroy(ExpenseType $type): RedirectResponse
    {
        $this->ensureAdmin();

        // Check if type is in use
        if ($type->expenses()->count() > 0) {
            return redirect()->route('expense-types.index')
                ->with('error', __('expense_types.cannot_delete_in_use'));
        }

        $type->delete();

        return redirect()->route('expense-types.index')->with('success', __('expense_types.deleted'));
    }
}
