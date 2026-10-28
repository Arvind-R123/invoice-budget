<?php
namespace Tests\Feature;
use App\Models\{SalesInvoice,Customer}; use Illuminate\Foundation\Testing\RefreshDatabase; use Tests\TestCase;
class InvoiceStatusTest extends TestCase { use RefreshDatabase; public function test_paid_status_is_derived_from_payments(): void { $customer=Customer::create(['name'=>'Test']);$invoice=SalesInvoice::create(['invoice_number'=>'T-1','customer_id'=>$customer->id,'issue_date'=>now(),'due_date'=>now()->addDay(),'total'=>'100']);$invoice->payments()->create(['payment_date'=>now(),'amount'=>'100']);$invoice->refreshStatus();$this->assertSame('Paid',$invoice->fresh()->status); } }
