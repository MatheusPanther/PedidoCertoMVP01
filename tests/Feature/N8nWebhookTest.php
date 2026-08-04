<?php

namespace Tests\Feature;

use App\Jobs\ProcessIntegrationEventJob;
use App\Models\IntegrationEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class N8nWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_accepts_signed_n8n_webhook_events_once(): void
    {
        Queue::fake();

        $body = [
            'event_id' => 'evt_demo_001',
            'event_type' => 'message.received',
            'organization_id' => null,
            'restaurant_id' => null,
            'branch_id' => null,
            'occurred_at' => now()->toISOString(),
            'source' => 'n8n',
            'data' => ['body' => 'Ola'],
        ];

        $timestamp = (string) now()->timestamp;
        $rawBody = json_encode($body, JSON_THROW_ON_ERROR);
        $signature = 'sha256='.hash_hmac('sha256', $timestamp.'.evt_demo_001.'.$rawBody, 'pedido-certo-demo-secret');

        $headers = [
            'X-Pedido-Certo-Event-Id' => 'evt_demo_001',
            'X-Pedido-Certo-Timestamp' => $timestamp,
            'X-Pedido-Certo-Signature' => $signature,
        ];

        $this->withHeaders($headers)->postJson('/api/v1/webhooks/n8n/events', $body)->assertAccepted();
        $this->withHeaders($headers)->postJson('/api/v1/webhooks/n8n/events', $body)->assertAccepted();

        $this->assertSame(1, IntegrationEvent::query()->where('event_id', 'evt_demo_001')->count());
        Queue::assertPushed(ProcessIntegrationEventJob::class, 1);
    }

    public function test_it_rejects_webhook_events_with_invalid_signatures(): void
    {
        $this->withHeaders([
            'X-Pedido-Certo-Event-Id' => 'evt_bad',
            'X-Pedido-Certo-Timestamp' => (string) now()->timestamp,
            'X-Pedido-Certo-Signature' => 'sha256=invalid',
        ])->postJson('/api/v1/webhooks/n8n/events', [
            'event_id' => 'evt_bad',
            'event_type' => 'message.received',
            'occurred_at' => now()->toISOString(),
            'source' => 'n8n',
            'data' => [],
        ])->assertUnauthorized();
    }
}
