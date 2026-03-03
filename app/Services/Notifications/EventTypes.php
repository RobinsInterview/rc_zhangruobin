<?php

namespace App\Services\Notifications;

final class EventTypes
{
    public const USER_REGISTERED_FROM_ADS = 'user_registered_from_ads';

    public const SUBSCRIPTION_PAID = 'subscription_paid';

    public const ORDER_PURCHASED = 'order_purchased';

    public static function all(): array
    {
        return [
            self::USER_REGISTERED_FROM_ADS,
            self::SUBSCRIPTION_PAID,
            self::ORDER_PURCHASED,
        ];
    }
}
