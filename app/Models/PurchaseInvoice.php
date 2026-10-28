<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class PurchaseInvoice extends Invoice { protected $table='purchase_invoices'; public function supplier(): BelongsTo { return $this->belongsTo(Supplier::class); } }
