<x-layouts.app title="Integracoes">
    @if(session('status'))
        <div class="mb-4 rounded-lg border border-brand-100 bg-brand-50 px-4 py-3 text-sm font-medium text-brand-700">{{ session('status') }}</div>
    @endif

    <div class="space-y-5">
        <div class="flex flex-col justify-between gap-3 md:flex-row md:items-center">
            <div>
                <h1 class="text-2xl font-semibold text-pc-text">Integracao com n8n</h1>
                <p class="mt-1 text-sm text-pc-secondary">Webhook assinado, idempotencia por event_id, fila e reprocessamento.</p>
            </div>
            <form method="POST" action="{{ route('integrations.test-event') }}">
                @csrf
                <button class="rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white">Criar evento teste</button>
            </form>
        </div>

        <section class="rounded-lg border border-pc-border bg-white p-5 shadow-sm">
            <h2 class="font-semibold text-pc-text">Contrato do endpoint</h2>
            <div class="mt-4 grid gap-3 md:grid-cols-3">
                <div class="rounded-lg bg-pc-muted p-4 text-sm text-pc-secondary">POST /api/v1/webhooks/n8n/events</div>
                <div class="rounded-lg bg-pc-muted p-4 text-sm text-pc-secondary">HMAC SHA-256: timestamp.event_id.raw_body</div>
                <div class="rounded-lg bg-pc-muted p-4 text-sm text-pc-secondary">Resposta rapida HTTP 202</div>
            </div>
        </section>

        <section class="rounded-lg border border-pc-border bg-white shadow-sm">
            <div class="border-b border-pc-border p-4">
                <h2 class="font-semibold text-pc-text">Eventos recentes</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-pc-muted text-xs uppercase text-pc-secondary">
                        <tr>
                            <th class="px-4 py-3">Evento</th>
                            <th class="px-4 py-3">Tipo</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Criado em</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-pc-border">
                        @forelse($events as $event)
                            <tr>
                                <td class="px-4 py-3 font-medium text-pc-text">{{ $event->event_id }}</td>
                                <td class="px-4 py-3 text-pc-secondary">{{ $event->event_type }}</td>
                                <td class="px-4 py-3"><span class="rounded-md bg-brand-50 px-2 py-1 text-xs font-medium text-brand-700">{{ $event->status }}</span></td>
                                <td class="px-4 py-3 text-pc-secondary">{{ $event->created_at?->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3 text-right">
                                    <form method="POST" action="{{ route('integrations.reprocess', $event) }}">
                                        @csrf
                                        <button class="rounded-md border border-pc-border px-3 py-2 text-xs font-medium text-pc-secondary">Reprocessar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-sm text-pc-secondary">Nenhum evento recebido ainda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-layouts.app>
