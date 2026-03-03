<?php

namespace Tests\Unit;

use App\Services\Notifications\CodeEventDefinitionProvider;
use App\Services\Notifications\EventTypes;
use Tests\TestCase;

class CodeEventDefinitionProviderTest extends TestCase
{
    public function test_returns_definition_for_supported_event(): void
    {
        $provider = app(CodeEventDefinitionProvider::class);

        $definition = $provider->findByEventType(EventTypes::SUBSCRIPTION_PAID);

        $this->assertNotNull($definition);
        $this->assertSame(EventTypes::SUBSCRIPTION_PAID, $definition->eventType);
        $this->assertNotEmpty($definition->targetUrl);
    }

    public function test_returns_null_for_unsupported_event(): void
    {
        $provider = app(CodeEventDefinitionProvider::class);

        $definition = $provider->findByEventType('not_exists');

        $this->assertNull($definition);
    }
}
