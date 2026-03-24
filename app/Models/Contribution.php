<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contribution extends Model
{
    protected $fillable = [
        'user_id', 'chama_id', 'amount', 'original_amount',
        'original_currency', 'payment_method', 'transaction_ref',
        'transaction_code', 'status', 'notes', 'payment_response',
    ];

    protected $casts = [
        'amount'           => 'decimal:2',
        'original_amount'  => 'decimal:2',
        'payment_response' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function chama(): BelongsTo
    {
        return $this->belongsTo(Chama::class);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeThisMonth($query)
    {
        return $query
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
    }
}