@props(['compact' => false])

<div {{ $attributes->class('flex items-center gap-2') }}>
    <div class="grid size-9 place-items-center rounded-lg border border-brand-200 bg-brand-50 text-brand-600">
        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M20 6 9 17l-5-5" />
        </svg>
    </div>
    @unless($compact)
        <div class="leading-tight">
            <p class="text-sm font-semibold tracking-normal text-pc-text">Pedido Certo</p>
            <p class="text-xs text-pc-secondary">por Ampliize</p>
        </div>
    @endunless
</div>
