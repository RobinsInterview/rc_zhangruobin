<?php

namespace App\Services\Notifications;

final class OutboundResult
{
    public function __construct(
        public readonly bool $successful,
        public readonly bool $retryable,
        public readonly string $targetUrl,
        public readonly array $requestSnapshot,
        public readonly ?int $responseStatus,
        public readonly ?string $responseBodySnippet,
        public readonly ?string $errorClass,
        public readonly ?string $errorMessage,
        public readonly int $durationMs,
    ) {
    }
}
