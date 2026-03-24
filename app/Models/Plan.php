<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = [
        'name', 'slug', 'description',
        'price_kes', 'price_usd',
        'max_members',
        'has_pdf_export', 'has_email_notifications', 'has_audit_logs',
        'has_multiple_chamas', 'has_custom_branding',
        'has_priority_support', 'has_api_access',
        'is_active', 'sort_order',
    ];

    protected $casts = [
        'has_pdf_export'          => 'boolean',
        'has_email_notifications' => 'boolean',
        'has_audit_logs'          => 'boolean',
        'has_multiple_chamas'     => 'boolean',
        'has_custom_branding'     => 'boolean',
        'has_priority_support'    => 'boolean',
        'has_api_access'          => 'boolean',
        'is_active'               => 'boolean',
        'price_kes'               => 'decimal:2',
        'price_usd'               => 'decimal:2',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function chamas(): HasMany
    {
        return $this->hasMany(Chama::class);
    }

    public function isFree(): bool
    {
        return $this->slug === 'free';
    }

    public function isUnlimitedMembers(): bool
    {
        return $this->max_members === -1;
    }

    public function formattedPriceKes(): string
    {
        return $this->price_kes == 0
            ? 'Free'
            : 'KES ' . number_format($this->price_kes, 0) . '/mo';
    }

    public function formattedPriceUsd(): string
    {
        return $this->price_usd == 0
            ? 'Free'
            : '$' . number_format($this->price_usd, 2) . '/mo';
    }

    public function membersLabel(): string
    {
        return $this->max_members === -1
            ? 'Unlimited'
            : 'Up to ' . $this->max_members;
    }
}