<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class Invoice extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = ['issue_date' => 'date', 'due_date' => 'date', 'discount_value' => 'decimal:2', 'tax_rate' => 'decimal:2', 'subtotal' => 'decimal:2', 'discount_amount' => 'decimal:2', 'tax_amount' => 'decimal:2', 'total' => 'decimal:2'];

    public function items(): MorphMany
    {
        return $this->morphMany(InvoiceItem::class, 'invoice');
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'invoice');
    }

    public function getPaidAmountAttribute(): string
    {
        return number_format((float) $this->payments()->sum('amount'), 2, '.', '');
    }

    public function getStatusAttribute(): string
    {
        return bccomp($this->paid_amount, (string) $this->total, 2) >= 0 ? 'Paid' : ($this->due_date?->isBefore(today()) ? 'Overdue' : 'Pending');
    }

    public function scopeWithStatus(Builder $query, string $status): Builder
    {
        $paid = "(SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payments.invoice_type = '{$this->getMorphClass()}' AND payments.invoice_id = {$this->getTable()}.id)";

        return match ($status) {
            'Paid' => $query->whereRaw("$paid >= {$this->getTable()}.total"),
            'Overdue' => $query->whereRaw("$paid < {$this->getTable()}.total")->whereDate('due_date', '<', today()),
            'Pending' => $query->whereRaw("$paid < {$this->getTable()}.total")->whereDate('due_date', '>=', today()),
            default => $query,
        };
    }

    public function refreshStatus(): static
    {
        $this->setRawAttributes(array_merge($this->getAttributes(), ['status' => $this->status]));
        $this->saveQuietly();

        return $this;
    }
}
