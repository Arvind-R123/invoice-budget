<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Expense;
use App\Models\PurchaseInvoice;
use App\Models\SalesInvoice;
use App\Services\BudgetTracker;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function __invoke(BudgetTracker $tracker)
    {
        $rates = Cache::remember('currency.rates', now()->addMinutes((int) config('services.currency.cache_ttl', 60)), function () {
            try {
                return Http::timeout((int) config('services.currency.timeout', 5))->get(config('services.currency.url'))->json();
            } catch (\Throwable $exception) {
                Log::warning('Currency rates request failed', ['url' => config('services.currency.url'), 'exception' => get_class($exception)]);

                return null;
            }
        });
        $budgets = Budget::with('expenses')->latest()->take(5)->get();
        $budgetSummaries = $budgets->mapWithKeys(fn ($budget) => [$budget->id => $tracker->summarize($budget)]);
        $outstanding = SalesInvoice::with('payments')->get()->filter(fn ($invoice) => in_array($invoice->status, ['Pending', 'Overdue'], true))->sum(fn ($invoice) => (float) $invoice->total - (float) $invoice->paid_amount);

        return view('dashboard', ['sales' => SalesInvoice::sum('total'), 'purchases' => PurchaseInvoice::sum('total'), 'expenses' => Expense::sum('amount'), 'outstanding' => $outstanding, 'budgets' => $budgets, 'budgetSummaries' => $budgetSummaries, 'rates' => $rates]);
    }
}
