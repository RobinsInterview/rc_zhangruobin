<?php

namespace App\Services\Notifications;

use App\Models\Notification;
use App\Services\Notifications\Contracts\EventDefinitionProviderInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class NotificationIngestService
{
    public function __construct(
        private readonly EventDefinitionProviderInterface $eventDefinitionProvider,
    ) {
    }

    public function ingest(array $attributes): Notification
    {
        $eventType = (string) ($attributes['event_type'] ?? '');
        $definition = $this->eventDefinitionProvider->findByEventType($eventType);
        if ($definition === null) {
            throw ValidationException::withMessages([
                'event_type' => ['The provided event_type is not supported.'],
            ]);
        }

        $payload = (array) ($attributes['payload'] ?? []);
        $validatedPayload = $definition->payloadClass::validate($payload);

        $bizId = (string) ($attributes['biz_id'] ?? '');
        $idempotencyKey = (string) (($attributes['idempotency_key'] ?? null)
            ?: hash('sha256', implode(':', [$eventType, $bizId])));

        $notification = DB::transaction(function () use ($eventType, $bizId, $idempotencyKey, $validatedPayload) {
            return Notification::query()->firstOrCreate(
                ['idempotency_key' => $idempotencyKey],
                [
                    'event_type' => $eventType,
                    'biz_id' => $bizId,
                    'payload_json' => $validatedPayload,
                    'status' => 'queued',
                    'attempt_count' => 0,
                ],
            );
        });

        if ($notification->wasRecentlyCreated) {
            $eventClass = $definition->eventClass;

            // Queue backend is Redis in MVP. Future versions can switch this dispatch layer to Kafka.
            event(new $eventClass($notification->id));
        }

        return $notification;
    }
}
