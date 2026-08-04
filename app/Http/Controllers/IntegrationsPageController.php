<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessIntegrationEventJob;
use App\Models\IntegrationEvent;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class IntegrationsPageController extends Controller
{
    public function index(): View
    {
        return view('app.integrations', [
            'events' => IntegrationEvent::query()->latest()->limit(8)->get(),
        ]);
    }

    public function createTestEvent(): RedirectResponse
    {
        if (! IntegrationEvent::query()->where('event_id', 'evt_demo_panel')->exists()) {
            IntegrationEvent::query()->create([
                'event_id' => 'evt_demo_panel',
                'event_type' => 'message.received',
                'source' => 'n8n',
                'status' => 'queued',
                'payload' => [
                    'event_id' => 'evt_demo_panel',
                    'event_type' => 'message.received',
                    'occurred_at' => now()->toISOString(),
                    'source' => 'n8n',
                    'data' => ['body' => 'Teste criado pelo painel'],
                ],
                'occurred_at' => now(),
            ]);
        }

        return back()->with('status', 'Evento de teste registrado.');
    }

    public function reprocess(IntegrationEvent $event): RedirectResponse
    {
        ProcessIntegrationEventJob::dispatch((string) $event->getKey())->onQueue('webhooks');

        return back()->with('status', 'Evento enviado para reprocessamento.');
    }
}
