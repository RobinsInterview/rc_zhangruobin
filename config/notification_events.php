<?php

return [
    'targets' => [
        'user_registered_from_ads' => env('NOTIFY_AD_PLATFORM_URL', 'https://example.com/ad-platform/registrations'),
        'subscription_paid' => env('NOTIFY_CRM_URL', 'https://example.com/crm/contact-status'),
        'order_purchased' => env('NOTIFY_INVENTORY_URL', 'https://example.com/inventory/adjustments'),
    ],
];
