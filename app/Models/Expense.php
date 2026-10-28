<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Expense extends Model { protected $guarded=[]; protected $casts=['expense_date'=>'date','amount'=>'decimal:2']; public function budget(): BelongsTo { return $this->belongsTo(Budget::class); } }
