<?php

namespace Tests\Feature;

use App\Models\Budget;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\SalesInvoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinalRequirementsTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_guest_is_redirected_from_required_pages(): void
    {
        foreach ([route('dashboard'), route('invoices.index', 'sales'), route('expenses.index'), route('budgets.index'), route('reports.index')] as $url) {
            $this->get($url)->assertRedirect(route('login'));
        }
    }

    public function test_expense_crud_and_warning_form(): void
    {
        $user = $this->user();
        $budget = Budget::create(['name' => 'Small', 'period_type' => 'monthly', 'category' => 'Ops', 'amount' => 100, 'period_start' => '2026-01-01', 'period_end' => '2026-12-31']);
        $response = $this->actingAs($user)->post(route('expenses.store'), ['budget_id' => $budget->id, 'category' => 'Ops', 'expense_date' => '2026-06-01', 'amount' => 150, 'description' => 'Over budget']);
        $expense = Expense::first();
        $response->assertRedirect(route('budgets.show', $budget));
        $this->assertNotNull($expense);
        $this->actingAs($user)->get(route('expenses.edit', $expense))->assertOk()->assertSee("This expense exceeds the selected budget's remaining amount", false);
        $this->actingAs($user)->put(route('expenses.update', $expense), ['budget_id' => $budget->id, 'category' => 'Updated', 'expense_date' => '2026-06-02', 'amount' => 50, 'description' => 'Updated'])->assertRedirect();
        $this->actingAs($user)->delete(route('expenses.destroy', $expense))->assertRedirect();
        $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
    }

    public function test_past_due_pending_invoice_is_overdue_in_list(): void
    {
        $user = $this->user();
        $customer = Customer::create(['name' => 'Customer']);
        $invoice = SalesInvoice::create(['invoice_number' => 'OVERDUE', 'customer_id' => $customer->id, 'issue_date' => now()->subDays(5), 'due_date' => now()->subDay(), 'total' => 100]);
        $this->actingAs($user)->get(route('invoices.index', 'sales'))->assertOk()->assertSee('Overdue')->assertSee($invoice->invoice_number);
    }

    public function test_login_page_has_no_navbar(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('type="password"', false)
            ->assertDontSee('navbar', false)
            ->assertDontSee('Dashboard');
    }
}
