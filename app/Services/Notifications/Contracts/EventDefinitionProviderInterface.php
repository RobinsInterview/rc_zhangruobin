<?php

namespace App\Services\Notifications\Contracts;

use App\Services\Notifications\EventDefinition;

interface EventDefinitionProviderInterface
{
    public function findByEventType(string $eventType): ?EventDefinition;
}
