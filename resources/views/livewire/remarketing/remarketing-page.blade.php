<div class="space-y-5">
    <div>
        <h1 class="text-2xl font-semibold text-pc-text">Remarketing automatizado</h1>
        <p class="mt-1 text-sm text-pc-secondary">Gatilhos, delays, cooldown, horario de silencio e webhooks para n8n.</p>
    </div>
    <section class="grid gap-4 lg:grid-cols-3">
        @forelse($automations as $automation)
            <article class="rounded-lg border border-pc-border bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <h2 class="font-semibold text-pc-text">{{ $automation->name }}</h2>
                    <span class="rounded-md bg-brand-50 px-2 py-1 text-xs font-medium text-brand-700">{{ $automation->status }}</span>
                </div>
                <p class="mt-2 text-sm text-pc-secondary">{{ $automation->trigger }}</p>
                <p class="mt-5 text-2xl font-semibold text-pc-text">{{ \App\Support\BrazilFormat::money($automation->metrics['recovered_revenue'] ?? 0) }}</p>
                <p class="mt-1 text-xs text-pc-secondary">{{ $automation->metrics['executions'] ?? 0 }} execucoes · {{ $automation->metrics['recovered_orders'] ?? 0 }} pedidos recuperados</p>
                <div class="mt-4 flex gap-2">
                    <button wire:click="toggleAutomation('{{ $automation->id }}')" class="rounded-md bg-brand-50 px-3 py-2 text-sm font-semibold text-brand-700">{{ $automation->status === 'active' ? 'Pausar' : 'Ativar' }}</button>
                    <button wire:click="duplicateAutomation('{{ $automation->id }}')" class="rounded-md border border-pc-border px-3 py-2 text-sm font-medium text-pc-secondary">Duplicar</button>
                </div>
            </article>
        @empty
            <div class="rounded-lg border border-dashed border-pc-border bg-white p-6 text-sm text-pc-secondary">Nenhuma automacao cadastrada.</div>
        @endforelse
    </section>
</div>
