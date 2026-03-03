<?php

namespace App\Notifications\Payloads\Contracts;

interface NotificationPayloadContract
{
    public static function rules(): array;

    public static function validate(array $payload): array;
}
