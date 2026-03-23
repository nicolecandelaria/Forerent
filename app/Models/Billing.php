<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Billing extends Model
{
    use SoftDeletes, HasFactory;

    protected $primaryKey = 'billing_id';

    protected $fillable = [
        'lease_id', 'billing_type', 'billing_date', 'next_billing', 'due_date',
        'to_pay', 'amount', 'previous_balance', 'status', 'tenant_id'
    ];

    protected $casts = [
        'billing_date' => 'date',
        'next_billing' => 'date',
        'due_date' => 'date',
        'to_pay' => 'decimal:2',
        'amount' => 'decimal:2',
        'previous_balance' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saved(function (Billing $billing) {
            // Sync inflow ledger whenever a billing is newly paid or status changes to paid.
            if ($billing->status === 'Paid' && ($billing->wasRecentlyCreated || $billing->wasChanged('status'))) {
                $billing->ensureCreditTransaction();
            }
        });
    }

    public function ensureCreditTransaction(): void
    {
        // Avoid duplicate inflow records for the same billing.
        $alreadyExists = $this->transactions()
            ->where('transaction_type', 'Credit')
            ->where('category', 'Rent Payment')
            ->exists();

        if ($alreadyExists) {
            return;
        }

        $amount = (float) ($this->amount ?? 0);
        if ($amount <= 0) {
            $amount = (float) ($this->to_pay ?? 0);
        }

        if ($amount <= 0) {
            return;
        }

        $transactionDate = optional($this->billing_date)->toDateString() ?? now()->toDateString();

        $this->transactions()->create([
            'name' => 'Billing Payment #' . $this->billing_id,
            'reference_number' => sprintf('BILL-%d-%s', $this->billing_id, now()->format('YmdHis')),
            'transaction_type' => 'Credit',
            'category' => 'Rent Payment',
            'transaction_date' => $transactionDate,
            'amount' => $amount,
            'is_recurring' => false,
        ]);
    }

    public function lease()
    {
        return $this->belongsTo(Lease::class, 'lease_id', 'lease_id');
    }

    /**
     * Relationship with transactions
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'billing_id', 'billing_id');
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class, 'billing_id', 'billing_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(BillingItem::class, 'billing_id', 'billing_id');
    }
}
