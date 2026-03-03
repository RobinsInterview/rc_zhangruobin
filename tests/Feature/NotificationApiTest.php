<?php

namespace Tests\Feature;

use App\Enums\NotificationStatus;
use App\Events\UserRegisteredFromAdsNotificationRequested;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class NotificationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_accepts_supported_event_and_returns_202(): void
    {
        Event::fake();

        $response = $this->postJson('/api/v1/notifications', [
            'event_type' => 'user_registered_from_ads',
            'biz_id' => 'BIZ-1001',
            'payload' => [
                'user_id' => 'U-1',
                'email' => 'user@example.com',
                'ad_source' => 'google_ads',
            ],
        ]);

        $response->assertStatus(202)
            ->assertJsonStructure(['notification_id', 'status']);

        $notificationId = $response->json('notification_id');
        $this->assertNotEmpty($notificationId);

        $this->assertDatabaseHas('notifications', [
            'id' => $notificationId,
            'event_type' => 'user_registered_from_ads',
            'status' => NotificationStatus::Queued->value,
        ]);

        Event::assertDispatched(UserRegisteredFromAdsNotificationRequested::class);
    }

    public function test_rejects_unknown_event_type(): void
    {
        $response = $this->postJson('/api/v1/notifications', [
            'event_type' => 'unknown_event_type',
            'biz_id' => 'BIZ-1002',
            'payload' => [
                'foo' => 'bar',
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['event_type']);
    }

    public function test_enforces_idempotency_and_dispatches_event_once(): void
    {
        Event::fake();

        $payload = [
            'event_type' => 'user_registered_from_ads',
            'biz_id' => 'BIZ-1003',
            'idempotency_key' => 'idem-1003',
            'payload' => [
                'user_id' => 'U-1003',
                'email' => 'u1003@example.com',
                'ad_source' => 'facebook_ads',
            ],
        ];

        $first = $this->postJson('/api/v1/notifications', $payload)->assertStatus(202);
        $second = $this->postJson('/api/v1/notifications', $payload)->assertStatus(202);

        $this->assertSame($first->json('notification_id'), $second->json('notification_id'));
        $this->assertDatabaseCount('notifications', 1);
        Event::assertDispatchedTimes(UserRegisteredFromAdsNotificationRequested::class, 1);
    }

    public function test_can_query_notification_status(): void
    {
        $notification = Notification::query()->create([
            'event_type' => 'order_purchased',
            'biz_id' => 'BIZ-2001',
            'idempotency_key' => 'idem-2001',
            'payload_json' => [
                'user_id' => 'U-2001',
                'order_id' => 'O-2001',
                'sku' => 'SKU-1',
                'quantity' => 1,
            ],
            'status' => NotificationStatus::Retrying,
            'attempt_count' => 2,
            'last_error_code' => '500',
            'last_error_msg' => 'Temporary failure',
            'next_retry_at' => now()->addMinute(),
        ]);

        $response = $this->getJson("/api/v1/notifications/{$notification->id}");

        $response->assertOk()
            ->assertJsonPath('notification_id', $notification->id)
            ->assertJsonPath('status', NotificationStatus::Retrying->value)
            ->assertJsonPath('attempt_count', 2)
            ->assertJsonPath('last_error.code', '500');
    }
}
