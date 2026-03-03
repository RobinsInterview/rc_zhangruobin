<?php

namespace App\Services\Notifications;

use App\Events\OrderPurchasedNotificationRequested;
use App\Events\SubscriptionPaidNotificationRequested;
use App\Events\UserRegisteredFromAdsNotificationRequested;
use App\Notifications\Payloads\OrderPurchasedPayload;
use App\Notifications\Payloads\SubscriptionPaidPayload;
use App\Notifications\Payloads\UserRegisteredFromAdsPayload;
use App\Services\Notifications\Contracts\EventDefinitionProviderInterface;
use App\Services\Notifications\Notifiers\AdPlatformNotifier;
use App\Services\Notifications\Notifiers\CrmNotifier;
use App\Services\Notifications\Notifiers\InventoryNotifier;

class CodeEventDefinitionProvider implements EventDefinitionProviderInterface
{
    public function findByEventType(string $eventType): ?EventDefinition
    {
        // MVP uses code mapping. This can be replaced by a DB-backed provider in a future version.
        $definitions = [
            EventTypes::USER_REGISTERED_FROM_ADS => new EventDefinition(
                eventType: EventTypes::USER_REGISTERED_FROM_ADS,
                eventClass: UserRegisteredFromAdsNotificationRequested::class,
                payloadClass: UserRegisteredFromAdsPayload::class,
                notifierClass: AdPlatformNotifier::class,
                targetUrl: (string) config('notification_events.targets.user_registered_from_ads'),
            ),
            EventTypes::SUBSCRIPTION_PAID => new EventDefinition(
                eventType: EventTypes::SUBSCRIPTION_PAID,
                eventClass: SubscriptionPaidNotificationRequested::class,
                payloadClass: SubscriptionPaidPayload::class,
                notifierClass: CrmNotifier::class,
                targetUrl: (string) config('notification_events.targets.subscription_paid'),
            ),
            EventTypes::ORDER_PURCHASED => new EventDefinition(
                eventType: EventTypes::ORDER_PURCHASED,
                eventClass: OrderPurchasedNotificationRequested::class,
                payloadClass: OrderPurchasedPayload::class,
                notifierClass: InventoryNotifier::class,
                targetUrl: (string) config('notification_events.targets.order_purchased'),
            ),
        ];

        return $definitions[$eventType] ?? null;
    }
}
