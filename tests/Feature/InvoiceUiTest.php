<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\PurchaseInvoice;
use App\Models\SalesInvoice;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceUiTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_sales_and_purchase_show_pages_load(): void
    {
        $user = $this->user();
        $customer = Customer::create(['name' => 'Customer']);
        $sales = SalesInvoice::create(['invoice_number' => 'S-1', 'customer_id' => $customer->id, 'issue_date' => now(), 'due_date' => now()->addDay(), 'subtotal' => 100, 'total' => 100]);
        $supplier = Supplier::create(['name' => 'Supplier']);
        $purchase = PurchaseInvoice::create(['invoice_number' => 'P-1', 'supplier_id' => $supplier->id, 'issue_date' => now(), 'due_date' => now()->addDay(), 'subtotal' => 100, 'total' => 100]);
        $this->actingAs($user)->get(route('invoices.show', ['sales', $sales]))->assertOk()->assertSee('S-1');
        $this->actingAs($user)->get(route('invoices.show', ['purchase', $purchase]))->assertOk()->assertSee('P-1');
    }

    public function test_dashboard_has_navbar_before_main_and_no_blade_directive(): void
    {
        $response = $this->actingAs($this->user())->get(route('dashboard'))->assertOk();
        $html = $response->getContent();
        $this->assertStringNotContainsString('@endsection', $html);
        $this->assertLessThan(strpos($html, '<main'), strpos($html, '<nav'));
    }
}
