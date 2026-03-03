<?php

namespace App\Listeners;

use App\Events\UserRegisteredFromAdsNotificationRequested;
use App\Services\Notifications\EventTypes;
use App\Services\Notifications\NotificationDeliveryService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Throwable;

class UserRegisteredFromAdsListener implements ShouldQueue
{
    use InteractsWithQueue;

    public int $tries = 6;

    public function backoff(): array
    {
        return [60, 300, 1800];
    }

    public function handle(UserRegisteredFromAdsNotificationRequested $event, NotificationDeliveryService $deliveryService): void
    {
        $deliveryService->deliver($event->notificationId, EventTypes::USER_REGISTERED_FROM_ADS, $this->attempts());
    }

    public function failed(UserRegisteredFromAdsNotificationRequested $event, Throwable $exception): void
    {
        app(NotificationDeliveryService::class)->markDead($event->notificationId, $exception->getMessage(), 'max_retries_exceeded');
    }
}
