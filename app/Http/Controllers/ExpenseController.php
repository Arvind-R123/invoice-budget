<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseRequest;
use App\Models\Budget;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $expenses = Expense::with('budget')
            ->when($request->filled('search'), fn ($query) => $query->where(function ($query) use ($request) {
                $query->where('description', 'like', '%'.$request->string('search').'%')
                    ->orWhere('category', 'like', '%'.$request->string('search').'%');
            }))
            ->when($request->filled('budget_id'), fn ($query) => $query->where('budget_id', $request->integer('budget_id')))
            ->when($request->filled('category'), fn ($query) => $query->where('category', $request->string('category')))
            ->when($request->filled('from'), fn ($query) => $query->whereDate('expense_date', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($query) => $query->whereDate('expense_date', '<=', $request->date('to')))
            ->latest('expense_date')->paginate(15)->withQueryString();

        return view('expenses.index', ['expenses' => $expenses, 'budgets' => Budget::orderBy('name')->get(), 'categories' => Expense::query()->distinct()->orderBy('category')->pluck('category')]);
    }

    public function create()
    {
        return view('expenses.form', ['expense' => new Expense, 'budgets' => Budget::latest()->get()]);
    }

    public function store(ExpenseRequest $request)
    {
        $data = $request->validated();
        $this->ensureInPeriod($data);
        Expense::create($data);

        return redirect()->route('budgets.show', $data['budget_id'])->with('success', 'Expense created.');
    }

    public function edit(Expense $expense)
    {
        return view('expenses.form', ['expense' => $expense, 'budgets' => Budget::latest()->get()]);
    }

    public function update(ExpenseRequest $request, Expense $expense)
    {
        $data = $request->validated();
        $this->ensureInPeriod($data);
        $expense->update($data);

        return redirect()->route('budgets.show', $data['budget_id'])->with('success', 'Expense updated.');
    }

    public function destroy(Expense $expense)
    {
        $budget = $expense->budget_id;
        $expense->delete();

        return redirect()->route('budgets.show', $budget)->with('success', 'Expense deleted.');
    }

    private function ensureInPeriod(array $data): void
    {
        $budget = Budget::findOrFail($data['budget_id']);
        $date = Carbon::parse($data['expense_date']);
        if ($date->lt($budget->period_start) || $date->gt($budget->period_end)) {
            throw ValidationException::withMessages(['expense_date' => 'The expense date must be within the budget period.']);
        }
    }
}
