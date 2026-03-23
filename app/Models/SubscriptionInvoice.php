<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionInvoice extends Model
{
    protected $fillable = [
        'subscription_id', 'chama_id', 'invoice_number',
        'amount_kes', 'amount_usd', 'currency',
        'payment_method', 'transaction_code',
        'status', 'due_date', 'paid_at',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'paid_at'  => 'datetime',
        'amount_kes' => 'decimal:2',
        'amount_usd' => 'decimal:2',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function chama(): BelongsTo
    {
        return $this->belongsTo(Chama::class);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public static function generateInvoiceNumber(): string
    {
        $last = static::latest()->value('invoice_number');
        $num  = $last ? (int) substr($last, 4) + 1 : 1;
        return 'INV-' . str_pad($num, 6, '0', STR_PAD_LEFT);
    }
}