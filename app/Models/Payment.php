<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\MorphTo;
class Payment extends Model { protected $guarded=[]; protected $casts=['payment_date'=>'date','amount'=>'decimal:2']; public function invoice(): MorphTo { return $this->morphTo(); } }
