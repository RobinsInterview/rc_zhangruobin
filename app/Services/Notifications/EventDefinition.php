<?php

namespace App\Services\Notifications;

final class EventDefinition
{
    /**
     * @param class-string $eventClass
     * @param class-string $payloadClass
     * @param class-string $notifierClass
     */
    public function __construct(
        public readonly string $eventType,
        public readonly string $eventClass,
        public readonly string $payloadClass,
        public readonly string $notifierClass,
        public readonly string $targetUrl,
        public readonly string $method = 'POST',
        public readonly array $headers = [],
        public readonly int $timeoutSeconds = 5,
    ) {
    }
}
