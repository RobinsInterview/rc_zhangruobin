<?php

namespace App\Services\Notifications\Contracts;

use App\Models\Notification;
use App\Services\Notifications\EventDefinition;
use App\Services\Notifications\OutboundResult;

interface OutboundNotifierInterface
{
    public function send(Notification $notification, EventDefinition $definition): OutboundResult;
}
