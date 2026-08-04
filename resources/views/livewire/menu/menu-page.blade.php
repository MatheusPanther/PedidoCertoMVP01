<div class="space-y-5">
    <div class="flex flex-col justify-between gap-3 md:flex-row md:items-center">
        <div>
            <h1 class="text-2xl font-semibold text-pc-text">Cardapio e produtos</h1>
            <p class="mt-1 text-sm text-pc-secondary">Categorias, produtos, variacoes, disponibilidade, estoque e pre-visualizacao mobile.</p>
        </div>
        <button wire:click="openCreateModal" class="rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white">Novo produto</button>
    </div>
    <section class="grid gap-4 xl:grid-cols-[1fr_360px]">
        <div class="rounded-lg border border-pc-border bg-white shadow-sm">
            <div class="grid grid-cols-4 border-b border-pc-border bg-pc-muted px-4 py-3 text-xs font-semibold uppercase text-pc-secondary">
                <span>Produto</span><span>Categoria</span><span>Preco</span><span>Status</span>
            </div>
            <div class="divide-y divide-pc-border">
                @forelse($products as $product)
                    <div class="grid grid-cols-4 items-center px-4 py-4 text-sm">
                        <span class="font-medium text-pc-text">{{ $product->name }}</span>
                        <span class="text-pc-secondary">{{ $product->category }}</span>
                        <span class="font-semibold text-pc-text">{{ \App\Support\BrazilFormat::money($product->price) }}</span>
                        <button wire:click="toggleProduct('{{ $product->id }}')" class="w-fit rounded-md px-2 py-1 text-xs font-medium {{ $product->available ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">{{ $product->available ? 'Disponivel' : 'Esgotado' }}</button>
                    </div>
                @empty
                    <div class="p-6 text-sm text-pc-secondary">Nenhum produto cadastrado.</div>
                @endforelse
            </div>
        </div>
        <aside class="rounded-[28px] border border-pc-border bg-white p-4 shadow-sm">
            <div class="rounded-[22px] border border-pc-border bg-pc-muted p-4">
                <p class="text-xs font-medium uppercase text-pc-secondary">Preview mobile</p>
                <h2 class="mt-2 text-lg font-semibold text-pc-text">Pedido Certo Burger</h2>
                <div class="mt-4 space-y-3">
                    @forelse($products as $product)
                        <div class="rounded-lg border border-pc-border bg-white p-3">
                            <p class="font-medium text-pc-text">{{ $product->name }}</p>
                            <p class="text-sm text-pc-secondary">{{ $product->category }}</p>
                            <p class="mt-2 font-semibold text-brand-700">{{ \App\Support\BrazilFormat::money($product->price) }}</p>
                        </div>
                    @empty
                        <div class="rounded-lg border border-dashed border-pc-border bg-white p-3 text-sm text-pc-secondary">Crie o primeiro produto para aparecer no preview.</div>
                    @endforelse
                </div>
            </div>
        </aside>
    </section>

    @if($createModalOpen)
        <div class="fixed inset-0 z-50 grid place-items-center bg-black/20 p-4">
            <form wire:submit="createProduct" class="w-full max-w-lg rounded-lg border border-pc-border bg-white p-5 shadow-xl">
                <h2 class="text-lg font-semibold text-pc-text">Novo produto</h2>
                <div class="mt-4 grid gap-3">
                    <input wire:model="name" class="rounded-md border border-pc-border px-3 py-2 text-sm" placeholder="Nome do produto">
                    <input wire:model="category" class="rounded-md border border-pc-border px-3 py-2 text-sm" placeholder="Categoria">
                    <input wire:model="price" type="number" min="1" step="0.01" class="rounded-md border border-pc-border px-3 py-2 text-sm" placeholder="Preco">
                </div>
                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" wire:click="$set('createModalOpen', false)" class="rounded-md border border-pc-border px-4 py-2 text-sm font-medium text-pc-secondary">Cancelar</button>
                    <button class="rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white">Salvar</button>
                </div>
            </form>
        </div>
    @endif
</div>
