<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\SoftDeletes; use Illuminate\Database\Eloquent\Relations\HasMany;
class Product extends Model { use SoftDeletes; protected $guarded=[]; protected $casts=['unit_price'=>'decimal:2','tax_rate'=>'decimal:2']; public function items(): HasMany { return $this->hasMany(InvoiceItem::class); } }
