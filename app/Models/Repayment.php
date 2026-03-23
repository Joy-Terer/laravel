<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Repayment extends Model
{
    protected $fillable = [
        'loan_id', 'user_id', 'amount_paid',
        'balance_remaining', 'payment_method',
        'transaction_ref', 'payment_date',
    ];

    protected $casts = [
        'amount_paid'       => 'decimal:2',
        'balance_remaining' => 'decimal:2',
        'payment_date'      => 'date',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}