<?php

namespace App\Services\Notifications;

use App\Models\Notification;

class NotificationQueryService
{
    public function findOrFail(string $notificationId): Notification
    {
        return Notification::query()->findOrFail($notificationId);
    }
}
