<?php

namespace App\Services\Notifications\Notifiers;

use App\Models\Notification;
use App\Services\Notifications\Contracts\OutboundNotifierInterface;
use App\Services\Notifications\EventDefinition;
use App\Services\Notifications\OutboundResult;

class CrmNotifier extends BaseHttpNotifier implements OutboundNotifierInterface
{
    public function send(Notification $notification, EventDefinition $definition): OutboundResult
    {
        $payload = $notification->payload_json;

        return $this->sendHttp($definition, [
            'contact_id' => $payload['contact_id'],
            'status' => 'subscribed',
            'plan' => $payload['subscribed_plan'],
            'paid_at' => $payload['paid_at'],
            'user_id' => $payload['user_id'],
        ]);
    }
}
