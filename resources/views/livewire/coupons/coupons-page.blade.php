<div class="space-y-5">
    <div class="flex flex-col justify-between gap-3 md:flex-row md:items-center">
        <div>
            <h1 class="text-2xl font-semibold text-pc-text">Cupons</h1>
            <p class="mt-1 text-sm text-pc-secondary">Regras avancadas, limites antiabuso e atribuicao de receita.</p>
        </div>
        <button wire:click="openCreateModal" class="rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white">Novo cupom</button>
    </div>
    <section class="grid gap-4 lg:grid-cols-3">
        @forelse($coupons as $coupon)
            <article class="rounded-lg border border-pc-border bg-white p-5 shadow-sm">
                <span class="rounded-md bg-brand-50 px-2 py-1 text-xs font-medium text-brand-700">{{ $coupon->status }}</span>
                <h2 class="mt-4 text-xl font-semibold text-pc-text">{{ $coupon->code }}</h2>
                <p class="mt-2 text-sm text-pc-secondary">{{ $coupon->name }}</p>
                <p class="text-2xl font-semibold text-pc-text">{{ $coupon->type === 'percentage' ? $coupon->value.'%' : \App\Support\BrazilFormat::money($coupon->value) }}</p>
                <p class="mt-2 text-xs text-pc-secondary">Uso minimo {{ \App\Support\BrazilFormat::money($coupon->minimum_order) }} · {{ $coupon->uses_count }} usos</p>
                <button wire:click="toggleStatus('{{ $coupon->id }}')" class="mt-4 rounded-md border border-pc-border px-3 py-2 text-sm font-medium text-pc-secondary">{{ $coupon->status === 'active' ? 'Pausar' : 'Ativar' }}</button>
            </article>
        @empty
            <div class="rounded-lg border border-dashed border-pc-border bg-white p-6 text-sm text-pc-secondary">Nenhum cupom cadastrado.</div>
        @endforelse
    </section>

    @if($createModalOpen)
        <div class="fixed inset-0 z-50 grid place-items-center bg-black/20 p-4">
            <form wire:submit="createCoupon" class="w-full max-w-lg rounded-lg border border-pc-border bg-white p-5 shadow-xl">
                <h2 class="text-lg font-semibold text-pc-text">Novo cupom</h2>
                <div class="mt-4 grid gap-3">
                    <input wire:model="code" class="rounded-md border border-pc-border px-3 py-2 text-sm uppercase" placeholder="CODIGO">
                    <input wire:model="name" class="rounded-md border border-pc-border px-3 py-2 text-sm" placeholder="Nome interno">
                    <input wire:model="value" type="number" min="1" max="90" step="0.01" class="rounded-md border border-pc-border px-3 py-2 text-sm" placeholder="Desconto %">
                </div>
                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" wire:click="$set('createModalOpen', false)" class="rounded-md border border-pc-border px-4 py-2 text-sm font-medium text-pc-secondary">Cancelar</button>
                    <button class="rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white">Salvar</button>
                </div>
            </form>
        </div>
    @endif
</div>
