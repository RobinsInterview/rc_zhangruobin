<?php

namespace App\Services\Notifications;

use App\Enums\NotificationStatus;
use App\Models\Notification;
use App\Models\NotificationAttempt;
use App\Services\Notifications\Contracts\EventDefinitionProviderInterface;
use App\Services\Notifications\Contracts\OutboundNotifierInterface;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class NotificationDeliveryService
{
    public function __construct(
        private readonly EventDefinitionProviderInterface $eventDefinitionProvider,
    ) {
    }

    public function deliver(string $notificationId, string $eventType, int $attempt): void
    {
        $notification = Notification::query()->find($notificationId);
        if (! $notification instanceof Notification) {
            return;
        }

        $definition = $this->eventDefinitionProvider->findByEventType($eventType);
        if (! $definition instanceof EventDefinition) {
            $this->markDead($notificationId, 'Unknown event type in worker.');

            return;
        }

        $notifier = app($definition->notifierClass);
        if (! $notifier instanceof OutboundNotifierInterface) {
            throw new RuntimeException("Notifier {$definition->notifierClass} must implement OutboundNotifierInterface.");
        }

        $notification->update([
            'status' => NotificationStatus::Processing,
            'attempt_count' => max((int) $notification->attempt_count, $attempt),
        ]);

        $result = $notifier->send($notification, $definition);

        NotificationAttempt::query()->create([
            'notification_id' => $notification->id,
            'attempt_no' => $attempt,
            'target_url' => $result->targetUrl,
            'request_snapshot_json' => $result->requestSnapshot,
            'response_status' => $result->responseStatus,
            'response_body_snippet' => $result->responseBodySnippet,
            'error_class' => $result->errorClass,
            'error_message' => $result->errorMessage,
            'duration_ms' => $result->durationMs,
            'created_at' => now(),
        ]);

        Log::info('notification.delivery_attempt', [
            'notification_id' => $notification->id,
            'event_type' => $notification->event_type,
            'attempt' => $attempt,
            'status' => $result->successful ? 'success' : ($result->retryable ? 'retryable_error' : 'non_retryable_error'),
            'response_status' => $result->responseStatus,
            'target_url' => $result->targetUrl,
            'error_class' => $result->errorClass,
        ]);

        if ($result->successful) {
            $notification->update([
                'status' => NotificationStatus::Success,
                'last_error_code' => null,
                'last_error_msg' => null,
                'next_retry_at' => null,
            ]);

            return;
        }

        if ($result->retryable) {
            $notification->update([
                'status' => NotificationStatus::Retrying,
                'last_error_code' => (string) ($result->responseStatus ?? $result->errorClass ?? 'retryable_error'),
                'last_error_msg' => $result->errorMessage,
                'next_retry_at' => now()->addMinute(),
            ]);

            throw new RetryableDeliveryException($result->errorMessage ?? 'Retryable delivery failure.');
        }

        $this->markDead($notification->id, $result->errorMessage, (string) ($result->responseStatus ?? 'non_retryable_error'));
    }

    public function markDead(string $notificationId, ?string $message = null, ?string $errorCode = null): void
    {
        Notification::query()
            ->where('id', $notificationId)
            ->update([
                'status' => NotificationStatus::Dead,
                'last_error_code' => $errorCode,
                'last_error_msg' => $message,
                'next_retry_at' => null,
            ]);
    }
}
