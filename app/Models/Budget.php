<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\HasMany;
class Budget extends Model { protected $guarded=[]; protected $casts=['amount'=>'decimal:2','period_start'=>'date','period_end'=>'date']; public function expenses(): HasMany { return $this->hasMany(Expense::class); } public function getUsedAttribute(): string { return number_format((float)$this->expenses()->sum('amount'), 2, '.', ''); } public function getRemainingAttribute(): string { return number_format((float)$this->amount - (float)$this->used, 2, '.', ''); } }
