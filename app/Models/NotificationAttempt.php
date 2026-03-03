<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationAttempt extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'notification_id',
        'attempt_no',
        'target_url',
        'request_snapshot_json',
        'response_status',
        'response_body_snippet',
        'error_class',
        'error_message',
        'duration_ms',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'request_snapshot_json' => 'array',
            'response_status' => 'integer',
            'attempt_no' => 'integer',
            'duration_ms' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function notification(): BelongsTo
    {
        return $this->belongsTo(Notification::class);
    }
}
