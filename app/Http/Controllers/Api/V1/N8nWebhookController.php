<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\N8nWebhookRequest;
use App\Jobs\ProcessIntegrationEventJob;
use App\Models\IntegrationEvent;
use Illuminate\Http\JsonResponse;

class N8nWebhookController extends Controller
{
    public function __invoke(N8nWebhookRequest $request): JsonResponse
    {
        $payload = $request->validated();

        $event = IntegrationEvent::query()->firstOrCreate(
            ['event_id' => $payload['event_id']],
            [
                'organization_id' => $payload['organization_id'] ?? null,
                'restaurant_id' => $payload['restaurant_id'] ?? null,
                'branch_id' => $payload['branch_id'] ?? null,
                'event_type' => $payload['event_type'],
                'source' => $payload['source'],
                'payload' => $payload,
                'occurred_at' => $payload['occurred_at'],
                'status' => 'queued',
            ],
        );

        if ($event->wasRecentlyCreated) {
            ProcessIntegrationEventJob::dispatch((string) $event->getKey())->onQueue('webhooks');
        }

        return response()->json([
            'accepted' => true,
            'event_id' => $event->getAttribute('event_id'),
            'status' => $event->wasRecentlyCreated ? 'queued' : 'duplicate_ignored',
        ], 202);
    }
}
