<?php

namespace App\Services\Notifications\Notifiers;

use App\Models\Notification;
use App\Services\Notifications\Contracts\OutboundNotifierInterface;
use App\Services\Notifications\EventDefinition;
use App\Services\Notifications\OutboundResult;

class AdPlatformNotifier extends BaseHttpNotifier implements OutboundNotifierInterface
{
    public function send(Notification $notification, EventDefinition $definition): OutboundResult
    {
        $payload = $notification->payload_json;

        return $this->sendHttp($definition, [
            'external_user_id' => $payload['user_id'],
            'email' => $payload['email'],
            'source' => $payload['ad_source'],
            'event_time' => now()->toIso8601String(),
        ]);
    }
}
