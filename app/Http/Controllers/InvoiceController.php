<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceRequest;
use App\Http\Requests\PaymentRequest;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\PurchaseInvoice;
use App\Models\SalesInvoice;
use App\Models\Supplier;
use App\Services\InvoiceCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class InvoiceController extends Controller
{
    private function model(string $type): string
    {
        abort_unless(in_array($type, ['sales', 'purchase'], true), 404);

        return $type === 'purchase' ? PurchaseInvoice::class : SalesInvoice::class;
    }

    private function partyKey(string $type): string
    {
        return $type === 'purchase' ? 'supplier_id' : 'customer_id';
    }

    private function partyModel(string $type): string
    {
        return $type === 'purchase' ? Supplier::class : Customer::class;
    }

    public function index(Request $request, string $type)
    {
        $model = $this->model($type);
        $relation = $type === 'purchase' ? 'supplier' : 'customer';
        $invoices = $model::with($relation)->when($request->search, fn ($q, $s) => $q->where('invoice_number', 'like', "%$s%"))->when($request->status, fn ($q, $status) => $q->withStatus($status))->when($request->filled('from'), fn ($q) => $q->whereDate('issue_date', '>=', $request->from))->when($request->filled('to'), fn ($q) => $q->whereDate('issue_date', '<=', $request->to))->latest()->paginate(15)->withQueryString();

        return view('invoices.index', compact('invoices', 'type'));
    }

    public function create(string $type)
    {
        return view('invoices.form', ['type' => $type, 'invoice' => null, 'parties' => $this->partyModel($type)::all(), 'products' => Product::all()]);
    }

    public function store(InvoiceRequest $request, InvoiceCalculator $calculator, string $type)
    {
        $invoice = $this->saveInvoice(new ($this->model($type)), $request->validated(), $type, $calculator);

        return redirect()->route('invoices.show', [$type, $invoice])->with('success', 'Invoice created.');
    }

    public function edit(string $type, $invoice)
    {
        $invoice = $this->model($type)::with('items')->findOrFail($invoice);

        return view('invoices.form', ['type' => $type, 'invoice' => $invoice, 'parties' => $this->partyModel($type)::all(), 'products' => Product::all()]);
    }

    public function update(InvoiceRequest $request, InvoiceCalculator $calculator, string $type, $invoice)
    {
        $invoice = $this->model($type)::findOrFail($invoice);
        $this->saveInvoice($invoice, $request->validated(), $type, $calculator);

        return redirect()->route('invoices.show', [$type, $invoice])->with('success', 'Invoice updated.');
    }

    private function saveInvoice(Invoice $invoice, array $data, string $type, InvoiceCalculator $calculator): Invoice
    {
        $invoice->fill(['invoice_number' => $invoice->invoice_number ?? strtoupper(Str::random(3)).'-'.now()->format('YmdHis'), 'issue_date' => $data['issue_date'], 'due_date' => $data['due_date'], 'discount_value' => $data['discount_value'] ?? 0, 'discount_type' => $data['discount_type'], 'tax_rate' => $data['tax_rate'] ?? 0, $this->partyKey($type) => $data['party_id']]);
        $invoice->fill($calculator->calculate($invoice, $data['items']));
        $invoice->save();
        $invoice->items()->delete();
        foreach ($data['items'] as $item) {
            $item['line_total'] = bcmul((string) $item['quantity'], (string) $item['unit_price'], 2);
            $invoice->items()->create($item);
        }

        return $invoice->refreshStatus();
    }

    public function show(string $type, $invoice)
    {
        $invoice = $this->model($type)::with(['items.product', 'payments', $type === 'purchase' ? 'supplier' : 'customer'])->findOrFail($invoice);

        return view('invoices.show', compact('invoice', 'type'));
    }

    public function destroy(string $type, $invoice)
    {
        $this->model($type)::findOrFail($invoice)->delete();

        return redirect()->route('invoices.index', $type)->with('success', 'Invoice deleted.');
    }

    public function payment(PaymentRequest $request, string $type, $invoice)
    {
        return DB::transaction(function () use ($request, $type, $invoice) {
            $invoice = $this->model($type)::lockForUpdate()->findOrFail($invoice);
            $amount = (string) $request->validated()['amount'];
            if (bccomp(bcadd($invoice->paid_amount, $amount, 2), (string) $invoice->total, 2) > 0) {
                throw ValidationException::withMessages(['amount' => 'Payment exceeds the invoice balance due.']);
            }$invoice->payments()->create($request->validated());
            $invoice->refreshStatus();

            return back()->with('success', 'Payment recorded.');
        });
    }

    public function destroyPayment(string $type, $invoice, $payment)
    {
        return DB::transaction(function () use ($type, $invoice, $payment) {
            $invoice = $this->model($type)::lockForUpdate()->findOrFail($invoice);
            $invoice->payments()->findOrFail($payment)->delete();
            $invoice->refreshStatus();

            return back()->with('success', 'Payment deleted.');
        });
    }
}
