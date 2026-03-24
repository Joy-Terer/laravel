<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'chama_id', 'name', 'email', 'phone',
        'password', 'role', 'status',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    // ── Relationships ──────────────────────────────────────────────
    public function chama(): BelongsTo
    {
        return $this->belongsTo(Chama::class);
    }

    public function contributions(): HasMany
    {
        return $this->hasMany(Contribution::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function repayments(): HasMany
    {
        return $this->hasMany(Repayment::class);
    }

    public function chamaNotifications(): HasMany
    {
        return $this->hasMany(ChamaNotification::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    // ── Scopes ─────────────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // ── Role helpers ───────────────────────────────────────────────
    public function isAdmin(): bool     { return $this->role === 'admin'; }
    public function isTreasurer(): bool { return $this->role === 'treasurer'; }
    public function isMember(): bool    { return $this->role === 'member'; }
    public function isActive(): bool    { return $this->status === 'active'; }

    // ── Utility ────────────────────────────────────────────────────
    public function totalContributed(): float
    {
        return $this->contributions()
            ->where('status', 'completed')
            ->sum('amount');
    }

    public function maxLoanAmount(): float
    {
        return $this->totalContributed() * 2;
    }

    public function hasActiveLoan(): bool
    {
        return $this->loans()
            ->whereIn('status', ['approved', 'active'])
            ->exists();
    }

    public function initials(): string
    {
        return strtoupper(substr($this->name, 0, 2));
    }
}