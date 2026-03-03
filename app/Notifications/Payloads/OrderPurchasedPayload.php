<?php

namespace App\Notifications\Payloads;

use App\Notifications\Payloads\Contracts\NotificationPayloadContract;
use Illuminate\Support\Facades\Validator;

class OrderPurchasedPayload implements NotificationPayloadContract
{
    public static function rules(): array
    {
        return [
            'user_id' => ['required', 'string', 'max:64'],
            'order_id' => ['required', 'string', 'max:64'],
            'sku' => ['required', 'string', 'max:64'],
            'quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public static function validate(array $payload): array
    {
        return Validator::make($payload, self::rules())->validate();
    }
}
