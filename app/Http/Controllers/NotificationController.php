<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNotificationRequest;
use App\Services\Auth\AuthenticatorInterface;
use App\Services\Notifications\NotificationIngestService;
use App\Services\Notifications\NotificationQueryService;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function store(
        StoreNotificationRequest $request,
        AuthenticatorInterface $authenticator,
        NotificationIngestService $notificationIngestService,
    ): JsonResponse {
        $authenticator->authenticate($request);

        $notification = $notificationIngestService->ingest($request->validated());

        return response()->json([
            'notification_id' => $notification->id,
            'status' => $notification->status->value,
        ], 202);
    }

    public function show(string $notificationId, NotificationQueryService $notificationQueryService): JsonResponse
    {
        $notification = $notificationQueryService->findOrFail($notificationId);

        return response()->json([
            'notification_id' => $notification->id,
            'event_type' => $notification->event_type,
            'status' => $notification->status->value,
            'attempt_count' => $notification->attempt_count,
            'last_error' => [
                'code' => $notification->last_error_code,
                'message' => $notification->last_error_msg,
            ],
            'next_retry_at' => optional($notification->next_retry_at)->toIso8601String(),
            'updated_at' => $notification->updated_at->toIso8601String(),
        ]);
    }
}
