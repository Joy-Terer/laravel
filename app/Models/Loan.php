<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    protected $fillable = [
        'user_id', 'chama_id', 'amount', 'balance',
        'status', 'purpose', 'repayment_period',
        'due_date', 'approved_by', 'decline_reason',
    ];

    protected $casts = [
        'amount'   => 'decimal:2',
        'balance'  => 'decimal:2',
        'due_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function chama(): BelongsTo
    {
        return $this->belongsTo(Chama::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function repayments(): HasMany
    {
        return $this->hasMany(Repayment::class);
    }

    public function isPending(): bool  { return $this->status === 'pending'; }
    public function isApproved(): bool { return $this->status === 'approved'; }
    public function isRepaid(): bool   { return $this->status === 'repaid'; }

    public function isOverdue(): bool
    {
        return $this->due_date
            && $this->due_date->isPast()
            && !$this->isRepaid();
    }

    public function progressPercent(): int
    {
        if ($this->amount <= 0) return 0;
        return (int) round((($this->amount - $this->balance) / $this->amount) * 100);
    }
}