<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo; use Illuminate\Database\Eloquent\Relations\MorphTo;
class InvoiceItem extends Model { protected $guarded=[]; protected $casts=['quantity'=>'decimal:2','unit_price'=>'decimal:2','tax_rate'=>'decimal:2','line_total'=>'decimal:2']; public function invoice(): MorphTo { return $this->morphTo(); } public function product(): BelongsTo { return $this->belongsTo(Product::class); } }
