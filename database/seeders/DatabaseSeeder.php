<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Product;
use App\Models\PurchaseInvoice;
use App\Models\SalesInvoice;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::updateOrCreate(['email' => env('ADMIN_EMAIL', 'admin@example.com')], ['name' => env('ADMIN_NAME', 'Admin'), 'password' => env('ADMIN_PASSWORD', 'password'), 'role' => 'admin']);
        $customers = [Customer::firstOrCreate(['email' => 'customer@example.com'], ['name' => 'Acme Retail']), Customer::firstOrCreate(['email' => 'second@example.com'], ['name' => 'Northwind Ltd']), Customer::firstOrCreate(['email' => 'third@example.com'], ['name' => 'Bharat Traders'])];
        $supplier = Supplier::firstOrCreate(['email' => 'supplier@example.com'], ['name' => 'Global Supplies']);
        Product::firstOrCreate(['name' => 'Consulting'], ['type' => 'service', 'unit_price' => 150, 'tax_rate' => 18]);
        Product::firstOrCreate(['name' => 'Office equipment'], ['type' => 'product', 'unit_price' => 500, 'tax_rate' => 18]);
        $budget = Budget::firstOrCreate(['name' => 'Operations '.now()->year], ['period_type' => 'yearly', 'category' => 'Operations', 'amount' => 10000, 'period_start' => now()->startOfYear(), 'period_end' => now()->endOfYear()]);
        $purchases = [];
        foreach (range(0, 8) as $index) {
            $date = now()->subMonths($index % 8)->startOfMonth()->addDays(5);
            $due = $index % 3 === 1 ? now()->addDays(30) : $date->copy()->subDays(5);
            $invoice = SalesInvoice::create(['invoice_number' => 'SEED-S-'.$index, 'customer_id' => $customers[$index % 3]->id, 'issue_date' => $date, 'due_date' => $due, 'subtotal' => 1000, 'tax_amount' => 0, 'total' => 1000, 'status' => 'Pending']);
            $invoice->items()->create(['description' => 'Seed consulting', 'quantity' => 1, 'unit_price' => 1000, 'tax_rate' => 0, 'line_total' => 1000]);
            if ($index % 3 === 0) {
                $invoice->payments()->create(['payment_date' => $date, 'amount' => 1000, 'method' => 'Bank']);
            }$invoice->refreshStatus();
            $purchase = PurchaseInvoice::create(['invoice_number' => 'SEED-P-'.$index, 'supplier_id' => $supplier->id, 'issue_date' => $date, 'due_date' => $date->copy()->addDays(30), 'subtotal' => 500, 'tax_amount' => 0, 'total' => 500, 'status' => 'Pending']);
            $purchases[] = $purchase;
            Expense::create(['budget_id' => $budget->id, 'expense_date' => $date, 'amount' => ($index + 1) * 25, 'description' => 'Seed '.$index, 'category' => ['Marketing', 'Travel', 'Software', 'Operations'][$index % 4]]);
        } foreach ($purchases as $index => $purchase) {
            if ($index < 3) {
                Payment::create(['invoice_type' => PurchaseInvoice::class, 'invoice_id' => $purchase->id, 'payment_date' => now(), 'amount' => 500, 'method' => 'Bank']);
            } elseif ($index === 3) {
                Payment::create(['invoice_type' => PurchaseInvoice::class, 'invoice_id' => $purchase->id, 'payment_date' => now(), 'amount' => 200, 'method' => 'UPI', 'reference' => 'PARTIAL-001']);
            }$purchase->refreshStatus();
        }
    }
}
