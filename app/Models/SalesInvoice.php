<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class SalesInvoice extends Invoice { protected $table='sales_invoices'; public function customer(): BelongsTo { return $this->belongsTo(Customer::class); } }
