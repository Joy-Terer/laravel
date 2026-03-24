<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id', 'action', 'description', 'ip_address', 'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Static helper — call from anywhere in the app:
     * AuditLog::log('contribution.completed', 'Joy contributed KES 2000');
     */
    public static function log(
        string $action,
        string $description,
        array  $metadata = []
    ): void {
        static::create([
            'user_id'     => auth()->id(),
            'action'      => $action,
            'description' => $description,
            'ip_address'  => request()->ip(),
            'metadata'    => $metadata ?: null,
        ]);
    }
}