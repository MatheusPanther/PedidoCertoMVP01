<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class N8nWebhookRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'event_id' => ['required', 'string', 'max:120'],
            'event_type' => ['required', 'string', 'max:120'],
            'organization_id' => ['nullable', 'string', 'max:32'],
            'restaurant_id' => ['nullable', 'string', 'max:32'],
            'branch_id' => ['nullable', 'string', 'max:32'],
            'occurred_at' => ['required', 'date'],
            'source' => ['required', 'string', 'max:50'],
            'data' => ['required', 'array'],
        ];
    }
}
