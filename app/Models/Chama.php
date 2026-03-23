<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Chama extends Model
{
    protected $fillable = [
        'name', 'description', 'code', 'balance',
        'contribution_amount', 'contribution_frequency',
        'admin_id', 'plan_id', 'subdomain', 'logo',
        'primary_color', 'is_active',
    ];

    protected $casts = [
        'balance'             => 'decimal:2',
        'contribution_amount' => 'decimal:2',
        'is_active'           => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────────────
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function contributions(): HasMany
    {
        return $this->hasMany(Contribution::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(SubscriptionInvoice::class);
    }

    // ── Helpers ────────────────────────────────────────────────────
    public function activeSubscription(): ?Subscription
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where(fn($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>', now()))
            ->latest()
            ->first();
    }

    public function monthlyTotal(): float
    {
        return $this->contributions()
            ->where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');
    }

    public function canAddMember(): bool
    {
        $plan = $this->plan;
        if (!$plan) return false;
        if ($plan->max_members === -1) return true;
        return $this->members()->where('status', 'active')->count() < $plan->max_members;
    }

    public function hasFeature(string $feature): bool
    {
        $plan = $this->plan;
        if (!$plan) return false;
        $key = 'has_' . $feature;
        return isset($plan->$key) && $plan->$key;
    }

    public function isOnFreePlan(): bool
    {
        return $this->plan?->slug === 'free';
    }
}