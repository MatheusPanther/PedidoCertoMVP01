<div class="space-y-5">
    <div>
        <h1 class="text-2xl font-semibold text-pc-text">Central de atendimento omnichannel</h1>
        <p class="mt-1 text-sm text-pc-secondary">WhatsApp, Instagram, IA e atendimento humano na mesma operacao.</p>
    </div>

    <section class="grid min-h-[720px] gap-4 xl:grid-cols-[320px_1fr_360px]">
        <aside class="rounded-lg border border-pc-border bg-white shadow-sm">
            <div class="border-b border-pc-border p-4">
                <input wire:model.live.debounce.300ms="search" class="w-full rounded-md border border-pc-border px-3 py-2 text-sm" placeholder="Buscar conversas">
                <div class="mt-3 flex gap-2 text-xs">
                    @foreach(['all' => 'Todas', 'waiting_attendant' => 'Aguardando', 'ai_handling' => 'IA', 'human_handling' => 'Minhas'] as $key => $label)
                        <button wire:click="$set('filter', '{{ $key }}')" class="rounded-md px-2 py-1 font-medium {{ $filter === $key ? 'bg-brand-50 text-brand-700' : 'border border-pc-border text-pc-secondary' }}">{{ $label }}</button>
                    @endforeach
                </div>
            </div>
            <div class="divide-y divide-pc-border">
                @forelse($conversations as $conversation)
                    <button wire:click="selectConversation('{{ $conversation->id }}')" class="block w-full p-4 text-left hover:bg-pc-muted {{ $selected?->id === $conversation->id ? 'bg-brand-50/60' : '' }}">
                        <div class="flex items-center justify-between gap-2">
                            <p class="font-semibold text-pc-text">{{ $conversation->customer?->name ?? 'Cliente' }}</p>
                            <span class="text-xs text-pc-secondary">{{ $conversation->last_message_at?->diffForHumans(short: true) ?? '-' }}</span>
                        </div>
                        <p class="mt-1 text-xs text-brand-700">{{ ucfirst($conversation->channel->value) }} · {{ str($conversation->status->value)->replace('_', ' ')->title() }}</p>
                        <p class="mt-2 text-sm text-pc-secondary">{{ $conversation->messages()->latest('sent_at')->value('body') ?? 'Sem mensagens recentes.' }}</p>
                    </button>
                @empty
                    <div class="p-5 text-sm text-pc-secondary">
                        Nenhuma conversa encontrada para este filtro.
                    </div>
                @endforelse
            </div>
        </aside>

        <section class="flex rounded-lg border border-pc-border bg-white shadow-sm">
            <div class="flex min-w-0 flex-1 flex-col">
                @if($selected)
                    <div class="flex items-center justify-between border-b border-pc-border p-4">
                        <div>
                            <h2 class="font-semibold text-pc-text">{{ $selected->customer?->name ?? 'Cliente' }}</h2>
                            <p class="text-sm text-pc-secondary">SLA {{ $selected->last_message_at?->diffForHumans(short: true) ?? '-' }} · {{ ucfirst($selected->channel->value) }} · {{ str($selected->status->value)->replace('_', ' ')->title() }}</p>
                        </div>
                        <div class="flex gap-2">
                            <button wire:click="assumeConversation" class="rounded-md bg-brand-500 px-3 py-2 text-sm font-semibold text-white">Assumir</button>
                            <button wire:click="returnToAi" class="rounded-md border border-pc-border px-3 py-2 text-sm font-medium text-pc-secondary">Devolver para IA</button>
                        </div>
                    </div>
                    <div class="flex-1 space-y-4 overflow-y-auto bg-pc-muted p-5">
                        @forelse($messages as $message)
                            <div class="{{ $message->direction === 'outbound' ? 'ml-auto bg-brand-500 text-white' : 'border border-pc-border bg-white text-pc-text' }} max-w-[75%] rounded-lg p-3 text-sm">
                                {{ $message->body }}
                                <div class="mt-2 text-[11px] opacity-70">{{ $message->sent_at?->format('d/m H:i') }}</div>
                            </div>
                        @empty
                            <div class="rounded-lg border border-dashed border-pc-border bg-white p-6 text-center text-sm text-pc-secondary">
                                Esta conversa ainda nao possui mensagens.
                            </div>
                        @endforelse
                    </div>
                    <div class="border-t border-pc-border p-4">
                        <div class="mb-3 flex flex-wrap gap-2">
                            <button type="button" wire:click="$set('messageBody', 'Oi! Ja estou verificando seu pedido por aqui.')" class="rounded-md border border-pc-border px-3 py-1.5 text-xs font-medium text-pc-secondary">Resposta rapida</button>
                            <button type="button" wire:click="$set('messageBody', 'Segue o cardapio atualizado: /app/cardapio')" class="rounded-md border border-pc-border px-3 py-1.5 text-xs font-medium text-pc-secondary">Enviar cardapio</button>
                            <button type="button" wire:click="$set('messageBody', 'Cupom VOLTA10 aplicado para seu proximo pedido.')" class="rounded-md border border-pc-border px-3 py-1.5 text-xs font-medium text-pc-secondary">Enviar cupom</button>
                            <button type="button" wire:click="openOrderModal" class="rounded-md border border-pc-border px-3 py-1.5 text-xs font-medium text-pc-secondary">Criar pedido</button>
                        </div>
                        <form wire:submit="sendMessage" class="flex gap-2">
                            <input wire:model="messageBody" class="flex-1 rounded-md border border-pc-border px-3 py-2 text-sm" placeholder="Escreva uma resposta">
                            <button class="rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white">Enviar</button>
                        </form>
                        @error('messageBody') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                @else
                    <div class="grid flex-1 place-items-center bg-pc-muted p-6 text-center text-sm text-pc-secondary">
                        Selecione uma conversa ou ajuste os filtros para iniciar o atendimento.
                    </div>
                @endif
            </div>
        </section>

        <aside class="rounded-lg border border-pc-border bg-white p-5 shadow-sm">
            <h2 class="font-semibold text-pc-text">Perfil do cliente</h2>
            @if($profile)
                <div class="mt-4 space-y-3 text-sm">
                    <p><span class="text-pc-secondary">Telefone:</span> {{ $profile->phone }}</p>
                    <p><span class="text-pc-secondary">Bairro:</span> {{ $profile->neighborhood ?? '-' }}</p>
                    <p><span class="text-pc-secondary">Total gasto:</span> {{ \App\Support\BrazilFormat::money($profile->total_spent) }}</p>
                    <p><span class="text-pc-secondary">Pedidos:</span> {{ $profile->orders_count }}</p>
                    <p><span class="text-pc-secondary">Canal:</span> {{ $selected ? ucfirst($selected->channel->value) : '-' }}</p>
                </div>
                <h3 class="mt-6 text-sm font-semibold text-pc-text">Timeline</h3>
                <div class="mt-3 space-y-3 text-sm text-pc-secondary">
                    <p>Ultimo pedido em {{ $profile->last_order_at?->format('d/m/Y') ?? '-' }}.</p>
                    <p>Tags: {{ collect($profile->tags ?? [])->join(', ') ?: 'Sem tags' }}.</p>
                    <p>{{ $profile->accepts_campaigns ? 'Consentimento de campanhas ativo.' : 'Campanhas desativadas para este cliente.' }}</p>
                </div>
            @else
                <p class="mt-4 text-sm text-pc-secondary">O perfil aparece quando uma conversa e selecionada.</p>
            @endif
        </aside>
    </section>

    @if($orderModalOpen)
        <div class="fixed inset-0 z-50 grid place-items-center bg-black/20 p-4">
            <form wire:submit="createOrderFromConversation" class="w-full max-w-lg rounded-lg border border-pc-border bg-white p-5 shadow-xl">
                <h2 class="text-lg font-semibold text-pc-text">Criar pedido pela conversa</h2>
                <div class="mt-4 grid gap-3">
                    <input class="rounded-md border border-pc-border px-3 py-2 text-sm" value="{{ $profile?->name }}" disabled>
                    <input wire:model="orderProductName" class="rounded-md border border-pc-border px-3 py-2 text-sm" placeholder="Produto">
                    <input wire:model="orderQuantity" type="number" min="1" class="rounded-md border border-pc-border px-3 py-2 text-sm" placeholder="Quantidade">
                    <input wire:model="orderTotal" type="number" min="1" step="0.01" class="rounded-md border border-pc-border px-3 py-2 text-sm" placeholder="Total">
                    <p class="text-xs text-pc-secondary">Total formatado: {{ $formattedOrderTotal }}</p>
                </div>
                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" wire:click="$set('orderModalOpen', false)" class="rounded-md border border-pc-border px-4 py-2 text-sm font-medium text-pc-secondary">Cancelar</button>
                    <button class="rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white">Criar pedido</button>
                </div>
            </form>
        </div>
    @endif
</div>
