<div class="space-y-5">
    <div class="flex flex-col justify-between gap-3 md:flex-row md:items-center">
        <div>
            <h1 class="text-2xl font-semibold text-pc-text">Pedidos e operacao</h1>
            <p class="mt-1 text-sm text-pc-secondary">Kanban, lista, cozinha e expedicao com SLA e historico.</p>
        </div>
        <button wire:click="createManualOrder" wire:loading.attr="disabled" class="rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white disabled:opacity-60">
            <span wire:loading.remove>Criar pedido manual</span>
            <span wire:loading>Criando...</span>
        </button>
    </div>

    <div class="flex gap-2">
        @foreach(['kanban' => 'Kanban', 'lista' => 'Lista', 'cozinha' => 'Cozinha', 'expedicao' => 'Expedicao'] as $key => $label)
            <button wire:click="$set('view', '{{ $key }}')" class="rounded-md border px-3 py-2 text-sm font-medium {{ $view === $key ? 'border-brand-200 bg-brand-50 text-brand-700' : 'border-pc-border bg-white text-pc-secondary' }}">{{ $label }}</button>
        @endforeach
    </div>

    @if($view === 'kanban')
    <section class="grid gap-4 xl:grid-cols-4">
        @foreach($statuses as $statusValue => $status)
            <div class="rounded-lg border border-pc-border bg-white p-4 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-pc-text">{{ $status }}</h2>
                    <span class="rounded bg-pc-muted px-2 py-1 text-xs text-pc-secondary">{{ $orders->filter(fn ($order) => $order->status->value === $statusValue)->count() }}</span>
                </div>
                <div class="space-y-3">
                    @forelse($orders->filter(fn ($order) => $order->status->value === $statusValue) as $order)
                        <article class="rounded-lg border border-pc-border bg-pc-muted p-3">
                            <div class="flex items-center justify-between">
                                <p class="font-semibold text-pc-text">#{{ $order->number }}</p>
                                <span class="text-xs text-pc-secondary">{{ $order->estimated_delivery_at?->diffForHumans(short: true, parts: 1) ?? '-' }}</span>
                            </div>
                            <p class="mt-1 text-sm text-pc-secondary">{{ $order->customer?->name ?? 'Cliente' }}</p>
                            <p class="mt-3 text-lg font-semibold text-pc-text">{{ \App\Support\BrazilFormat::money($order->total) }}</p>
                            <div class="mt-3 grid grid-cols-2 gap-2">
                                <button wire:click="updateStatus('{{ $order->id }}', '{{ $statusValue === 'out_for_delivery' ? 'delivered' : 'preparing' }}')" class="rounded-md border border-pc-border bg-white px-3 py-2 text-xs font-medium text-pc-secondary">Alterar</button>
                                <button wire:click="openDrawer('{{ $order->id }}')" class="rounded-md bg-brand-50 px-3 py-2 text-xs font-semibold text-brand-700">Detalhes</button>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-lg border border-dashed border-pc-border bg-pc-muted p-4 text-sm text-pc-secondary">
                            Nenhum pedido neste status.
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </section>
    @else
        <section class="overflow-hidden rounded-lg border border-pc-border bg-white shadow-sm">
            <table class="w-full text-left text-sm">
                <thead class="bg-pc-muted text-xs uppercase text-pc-secondary">
                    <tr>
                        <th class="px-4 py-3">Pedido</th>
                        <th class="px-4 py-3">Cliente</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Total</th>
                        <th class="px-4 py-3">Acoes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-pc-border">
                    @forelse($orders as $order)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-pc-text">#{{ $order->number }}</td>
                            <td class="px-4 py-3 text-pc-secondary">{{ $order->customer?->name ?? 'Cliente' }}</td>
                            <td class="px-4 py-3 text-pc-secondary">{{ str($order->status->value)->replace('_', ' ')->title() }}</td>
                            <td class="px-4 py-3 text-pc-text">{{ \App\Support\BrazilFormat::money($order->total) }}</td>
                            <td class="px-4 py-3">
                                <button wire:click="openDrawer('{{ $order->id }}')" class="rounded-md bg-brand-50 px-3 py-2 text-xs font-semibold text-brand-700">Detalhes</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-pc-secondary">Nenhum pedido cadastrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    @endif

    @if($drawerOpen && $selectedOrder)
        <aside class="fixed inset-y-0 right-0 z-50 w-full max-w-md border-l border-pc-border bg-white p-5 shadow-xl">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-pc-text">Detalhes do pedido #{{ $selectedOrder->number }}</h2>
                <button wire:click="closeDrawer" class="grid size-9 place-items-center rounded-md border border-pc-border text-pc-secondary">×</button>
            </div>
            <div class="mt-6 space-y-4 text-sm">
                <div class="rounded-lg bg-pc-muted p-4">
                    <p class="font-medium text-pc-text">Cliente</p>
                    <p class="mt-1 text-pc-secondary">{{ $selectedOrder->customer?->name ?? 'Cliente' }} · {{ ucfirst($selectedOrder->channel->value) }} · origem {{ $selectedOrder->origin }}</p>
                </div>
                <div class="rounded-lg bg-pc-muted p-4">
                    <p class="font-medium text-pc-text">Itens</p>
                    @forelse($selectedOrder->items as $item)
                        <p class="mt-1 text-pc-secondary">{{ $item->quantity }}x {{ $item->product_name }} · {{ \App\Support\BrazilFormat::money($item->total) }}</p>
                    @empty
                        <p class="mt-1 text-pc-secondary">Nenhum item vinculado.</p>
                    @endforelse
                </div>
                <div class="rounded-lg bg-pc-muted p-4">
                    <p class="font-medium text-pc-text">Entrega</p>
                    <p class="mt-1 text-pc-secondary">{{ $selectedOrder->address ?? 'Retirada ou endereço ainda nao informado' }} · {{ $selectedOrder->estimated_delivery_at?->diffForHumans(short: true, parts: 1) ?? '-' }} · {{ str($selectedOrder->payment_status->value)->title() }}</p>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <button wire:click="updateStatus('{{ $selectedOrder->id }}', 'confirmed')" class="rounded-md border border-pc-border px-3 py-2 text-xs font-medium text-pc-secondary">Confirmar</button>
                    <button wire:click="updateStatus('{{ $selectedOrder->id }}', 'cancelled')" class="rounded-md border border-red-200 px-3 py-2 text-xs font-medium text-red-700">Cancelar</button>
                </div>
            </div>
        </aside>
    @endif
</div>
