<?php
namespace App\Services;
use App\Models\Invoice;
class InvoiceCalculator
{
    public function calculate(Invoice $invoice, array $items): array
    {
        $subtotal = '0.00'; $tax = '0.00';
        foreach ($items as $item) {
            $gross = bcmul((string)$item['quantity'], (string)$item['unit_price'], 4);
            $lineDiscount = ($item['discount_type'] ?? 'flat') === 'percent' ? bcdiv(bcmul($gross, (string)($item['discount_value'] ?? 0), 4), '100', 4) : (string)($item['discount_value'] ?? 0);
            $line = bcsub($gross, $lineDiscount, 4); $subtotal = bcadd($subtotal, $line, 4);
            $tax = bcadd($tax, bcdiv(bcmul($line, (string)($item['tax_rate'] ?? $invoice->tax_rate ?? 0), 4), '100', 4), 4);
        }
        $discount = ($invoice->discount_type ?? 'flat') === 'percent' ? bcdiv(bcmul($subtotal, (string)$invoice->discount_value, 4), '100', 4) : (string)$invoice->discount_value;
        $taxable = bcsub($subtotal, $discount, 4);
        if (!$items) { $tax = bcdiv(bcmul($taxable, (string)$invoice->tax_rate, 4), '100', 4); }
        if ($subtotal !== '0.0000' && $discount !== '0.0000') { $tax = bcdiv(bcmul($tax, $taxable, 4), $subtotal, 4); }
        return ['subtotal'=>bcadd($subtotal, '0', 2), 'discount_amount'=>bcadd($discount, '0', 2), 'tax_amount'=>bcadd($tax, '0', 2), 'total'=>bcadd(bcadd($taxable, $tax, 4), '0', 2)];
    }
}
