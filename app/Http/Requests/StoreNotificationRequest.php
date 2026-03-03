<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_type' => ['required', 'string', 'max:64'],
            'biz_id' => ['required', 'string', 'max:128'],
            'idempotency_key' => ['nullable', 'string', 'max:255'],
            'payload' => ['required', 'array'],
        ];
    }
}
