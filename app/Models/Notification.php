<?php

namespace App\Models;

use App\Enums\NotificationStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Notification extends Model
{
    use HasFactory;
    use HasUlids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'event_type',
        'biz_id',
        'idempotency_key',
        'payload_json',
        'status',
        'attempt_count',
        'last_error_code',
        'last_error_msg',
        'next_retry_at',
    ];

    protected function casts(): array
    {
        return [
            'payload_json' => 'array',
            'status' => NotificationStatus::class,
            'attempt_count' => 'integer',
            'next_retry_at' => 'datetime',
        ];
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(NotificationAttempt::class);
    }
}
