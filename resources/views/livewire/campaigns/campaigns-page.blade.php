<div class="space-y-5">
    <div class="flex flex-col justify-between gap-3 md:flex-row md:items-center">
        <div>
            <h1 class="text-2xl font-semibold text-pc-text">Campanhas</h1>
            <p class="mt-1 text-sm text-pc-secondary">Envios manuais por segmento com teste, lote, pausa e atribuicao de receita.</p>
        </div>
        <button wire:click="openCreateModal" class="rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white">Nova campanha</button>
    </div>
    <div class="rounded-lg border border-pc-border bg-white p-5 shadow-sm">
        <div class="grid gap-4 md:grid-cols-4">
            @foreach(['Campanhas' => $campaigns->count(), 'Agendadas' => $campaigns->where('status', 'scheduled')->count(), 'Destinatarios' => $campaigns->sum(fn ($campaign) => $campaign->metrics['recipients'] ?? 0), 'Pedidos' => $campaigns->sum(fn ($campaign) => $campaign->metrics['orders'] ?? 0)] as $label => $value)
                <div class="rounded-lg bg-pc-muted p-4">
                    <p class="text-sm text-pc-secondary">{{ $label }}</p>
                    <p class="mt-2 text-2xl font-semibold text-pc-text">{{ $value }}</p>
                </div>
            @endforeach
        </div>
    </div>
    <section class="rounded-lg border border-pc-border bg-white shadow-sm">
        <div class="divide-y divide-pc-border">
            @forelse($campaigns as $campaign)
                <article class="grid gap-3 p-4 md:grid-cols-[1fr_160px_180px] md:items-center">
                    <div>
                        <h2 class="font-semibold text-pc-text">{{ $campaign->name }}</h2>
                        <p class="mt-1 text-sm text-pc-secondary">{{ $campaign->template }}</p>
                    </div>
                    <span class="w-fit rounded-md bg-brand-50 px-2 py-1 text-xs font-medium text-brand-700">{{ $campaign->status }}</span>
                    <div class="flex gap-2">
                        <button wire:click="schedule('{{ $campaign->id }}')" class="rounded-md bg-brand-500 px-3 py-2 text-xs font-semibold text-white">Agendar</button>
                        <button wire:click="pause('{{ $campaign->id }}')" class="rounded-md border border-pc-border px-3 py-2 text-xs font-medium text-pc-secondary">Pausar</button>
                    </div>
                </article>
            @empty
                <div class="p-6 text-sm text-pc-secondary">Nenhuma campanha cadastrada.</div>
            @endforelse
        </div>
    </section>

    @if($createModalOpen)
        <div class="fixed inset-0 z-50 grid place-items-center bg-black/20 p-4">
            <form wire:submit="createCampaign" class="w-full max-w-lg rounded-lg border border-pc-border bg-white p-5 shadow-xl">
                <h2 class="text-lg font-semibold text-pc-text">Nova campanha</h2>
                <div class="mt-4 grid gap-3">
                    <input wire:model="name" class="rounded-md border border-pc-border px-3 py-2 text-sm" placeholder="Nome da campanha">
                    <textarea wire:model="template" rows="4" class="rounded-md border border-pc-border px-3 py-2 text-sm" placeholder="Mensagem"></textarea>
                </div>
                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" wire:click="$set('createModalOpen', false)" class="rounded-md border border-pc-border px-4 py-2 text-sm font-medium text-pc-secondary">Cancelar</button>
                    <button class="rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white">Salvar</button>
                </div>
            </form>
        </div>
    @endif
</div>
