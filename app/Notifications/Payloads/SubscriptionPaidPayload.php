<?php

namespace App\Notifications\Payloads;

use App\Notifications\Payloads\Contracts\NotificationPayloadContract;
use Illuminate\Support\Facades\Validator;

class SubscriptionPaidPayload implements NotificationPayloadContract
{
    public static function rules(): array
    {
        return [
            'user_id' => ['required', 'string', 'max:64'],
            'contact_id' => ['required', 'string', 'max:64'],
            'subscribed_plan' => ['required', 'string', 'max:64'],
            'paid_at' => ['required', 'date'],
        ];
    }

    public static function validate(array $payload): array
    {
        return Validator::make($payload, self::rules())->validate();
    }
}
