<div class="space-y-5">
    <div class="flex flex-col justify-between gap-3 md:flex-row md:items-center">
        <div>
            <h1 class="text-2xl font-semibold text-pc-text">CRM de clientes</h1>
            <p class="mt-1 text-sm text-pc-secondary">Segmentacao, historico, consentimentos, LTV e deduplicacao por telefone/e-mail.</p>
        </div>
        <button wire:click="openCreateModal" class="rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white">Novo cliente</button>
    </div>
    <div class="rounded-lg border border-pc-border bg-white shadow-sm">
        <div class="border-b border-pc-border p-4">
            <input wire:model.live="search" class="w-full max-w-md rounded-md border border-pc-border px-3 py-2 text-sm" placeholder="Buscar por nome, telefone, bairro ou segmento">
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-pc-muted text-xs uppercase text-pc-secondary">
                    <tr>
                        <th class="px-4 py-3">Cliente</th>
                        <th class="px-4 py-3">Contato</th>
                        <th class="px-4 py-3">Segmento</th>
                        <th class="px-4 py-3">Pedidos</th>
                        <th class="px-4 py-3">Total gasto</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-pc-border">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-pc-muted">
                            <td class="px-4 py-3 font-medium text-pc-text">{{ $customer->name }}</td>
                            <td class="px-4 py-3 text-pc-secondary">{{ $customer->phone }}</td>
                            <td class="px-4 py-3"><span class="rounded-md bg-brand-50 px-2 py-1 text-xs font-medium text-brand-700">{{ collect($customer->tags ?? [])->first() ?? 'Recorrente' }}</span></td>
                            <td class="px-4 py-3 text-pc-secondary">{{ $customer->orders_count }}</td>
                            <td class="px-4 py-3 font-semibold text-pc-text">{{ \App\Support\BrazilFormat::money($customer->total_spent) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-pc-secondary">Nenhum cliente encontrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($createModalOpen)
        <div class="fixed inset-0 z-50 grid place-items-center bg-black/20 p-4">
            <form wire:submit="createCustomer" class="w-full max-w-lg rounded-lg border border-pc-border bg-white p-5 shadow-xl">
                <h2 class="text-lg font-semibold text-pc-text">Novo cliente</h2>
                <div class="mt-4 grid gap-3">
                    <input wire:model="name" class="rounded-md border border-pc-border px-3 py-2 text-sm" placeholder="Nome">
                    <input wire:model="phone" class="rounded-md border border-pc-border px-3 py-2 text-sm" placeholder="Telefone">
                    <input wire:model="email" class="rounded-md border border-pc-border px-3 py-2 text-sm" placeholder="E-mail">
                    <input wire:model="neighborhood" class="rounded-md border border-pc-border px-3 py-2 text-sm" placeholder="Bairro">
                </div>
                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" wire:click="$set('createModalOpen', false)" class="rounded-md border border-pc-border px-4 py-2 text-sm font-medium text-pc-secondary">Cancelar</button>
                    <button class="rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white">Salvar</button>
                </div>
            </form>
        </div>
    @endif
</div>
