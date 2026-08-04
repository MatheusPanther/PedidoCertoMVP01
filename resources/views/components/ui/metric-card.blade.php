@props(['label', 'value', 'change' => null, 'tone' => 'info'])

@php
    $toneClasses = [
        'success' => 'text-green-700 bg-green-50 border-green-100',
        'warning' => 'text-amber-700 bg-amber-50 border-amber-100',
        'info' => 'text-blue-700 bg-blue-50 border-blue-100',
    ][$tone] ?? 'text-pc-secondary bg-pc-muted border-pc-border';
@endphp

<article class="rounded-lg border border-pc-border bg-white p-4 shadow-sm">
    <div class="flex items-start justify-between gap-3">
        <p class="text-sm font-medium text-pc-secondary">{{ $label }}</p>
        <span class="grid size-8 place-items-center rounded-md bg-brand-50 text-brand-600">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M4 19V5" />
                <path d="M4 19h16" />
                <path d="m7 15 4-4 3 3 5-7" />
            </svg>
        </span>
    </div>
    <div class="mt-4 flex items-end justify-between gap-2">
        <p class="text-2xl font-semibold tracking-normal text-pc-text">{{ $value }}</p>
        @if($change)
            <span class="rounded-md border px-2 py-1 text-xs font-medium {{ $toneClasses }}">{{ $change }}</span>
        @endif
    </div>
</article>
