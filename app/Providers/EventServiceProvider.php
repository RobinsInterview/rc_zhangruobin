<?php

namespace App\Providers;

use App\Events\OrderPurchasedNotificationRequested;
use App\Events\SubscriptionPaidNotificationRequested;
use App\Events\UserRegisteredFromAdsNotificationRequested;
use App\Listeners\OrderPurchasedListener;
use App\Listeners\SubscriptionPaidListener;
use App\Listeners\UserRegisteredFromAdsListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        UserRegisteredFromAdsNotificationRequested::class => [
            UserRegisteredFromAdsListener::class,
        ],
        SubscriptionPaidNotificationRequested::class => [
            SubscriptionPaidListener::class,
        ],
        OrderPurchasedNotificationRequested::class => [
            OrderPurchasedListener::class,
        ],
    ];
}
