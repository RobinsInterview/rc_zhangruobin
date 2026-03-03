<?php

namespace App\Services\Notifications\Notifiers;

use App\Models\Notification;
use App\Services\Notifications\Contracts\OutboundNotifierInterface;
use App\Services\Notifications\EventDefinition;
use App\Services\Notifications\OutboundResult;

class InventoryNotifier extends BaseHttpNotifier implements OutboundNotifierInterface
{
    public function send(Notification $notification, EventDefinition $definition): OutboundResult
    {
        $payload = $notification->payload_json;

        return $this->sendHttp($definition, [
            'order_id' => $payload['order_id'],
            'sku' => $payload['sku'],
            'quantity_change' => -1 * (int) $payload['quantity'],
            'user_id' => $payload['user_id'],
        ]);
    }
}
