<?php

namespace App\Services\Notifications\Notifiers;

use App\Services\Notifications\EventDefinition;
use App\Services\Notifications\OutboundResult;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

abstract class BaseHttpNotifier
{
    protected function sendHttp(EventDefinition $definition, array $body): OutboundResult
    {
        $startedAt = microtime(true);

        try {
            $response = Http::withHeaders($definition->headers)
                ->timeout($definition->timeoutSeconds)
                ->send($definition->method, $definition->targetUrl, ['json' => $body]);

            $statusCode = $response->status();
            $successful = $response->successful();
            $retryable = $statusCode === 408 || $statusCode === 429 || $statusCode >= 500;

            return new OutboundResult(
                successful: $successful,
                retryable: ! $successful && $retryable,
                targetUrl: $definition->targetUrl,
                requestSnapshot: $body,
                responseStatus: $statusCode,
                responseBodySnippet: Str::limit($response->body(), 1000, ''),
                errorClass: null,
                errorMessage: $successful ? null : 'Non-2xx response from upstream API.',
                durationMs: (int) round((microtime(true) - $startedAt) * 1000),
            );
        } catch (Throwable $exception) {
            return new OutboundResult(
                successful: false,
                retryable: true,
                targetUrl: $definition->targetUrl,
                requestSnapshot: $body,
                responseStatus: null,
                responseBodySnippet: null,
                errorClass: $exception::class,
                errorMessage: $exception->getMessage(),
                durationMs: (int) round((microtime(true) - $startedAt) * 1000),
            );
        }
    }
}
