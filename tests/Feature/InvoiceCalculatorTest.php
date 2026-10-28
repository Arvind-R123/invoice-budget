<?php
namespace Tests\Feature;
use App\Models\SalesInvoice; use App\Services\InvoiceCalculator; use Tests\TestCase;
class InvoiceCalculatorTest extends TestCase { public function test_invoice_calculation_uses_decimal_rules(): void { $invoice=new SalesInvoice(['discount_value'=>'10','discount_type'=>'percent','tax_rate'=>'18']); $result=(new InvoiceCalculator)->calculate($invoice,[['quantity'=>'2','unit_price'=>'100','tax_rate'=>'18','discount_value'=>'0','discount_type'=>'flat']]); $this->assertSame('200.00',$result['subtotal']); $this->assertSame('20.00',$result['discount_amount']); $this->assertSame('32.40',$result['tax_amount']); $this->assertSame('212.40',$result['total']); } }
