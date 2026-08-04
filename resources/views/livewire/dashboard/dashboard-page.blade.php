<div class="space-y-6">
    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end">
        <div>
            <p class="text-sm font-medium text-brand-700">Visao geral</p>
            <h1 class="mt-1 text-2xl font-semibold tracking-normal text-pc-text">Dashboard comercial e operacional</h1>
            <p class="mt-2 max-w-3xl text-sm text-pc-secondary">Enquanto o restaurante prepara os pedidos, o Pedido Certo atende, vende, organiza e faz os clientes voltarem.</p>
        </div>
        <a href="{{ route('campaigns') }}" class="rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-600">Ativar campanha recomendada</a>
    </div>

    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach($workspace['metrics'] as $metric)
            <x-ui.metric-card :label="$metric['label']" :value="$metric['value']" :change="$metric['change']" :tone="$metric['tone']" />
        @endforeach
    </section>

    <section class="grid gap-4 xl:grid-cols-[1.4fr_0.9fr]">
        <div class="rounded-lg border border-pc-border bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-semibold text-pc-text">Faturamento ao longo do tempo</h2>
                    <p class="text-sm text-pc-secondary">Receita por dia em BRL, comparada ao periodo anterior.</p>
                </div>
                <span class="rounded-md bg-brand-50 px-2 py-1 text-xs font-medium text-brand-700">Demo</span>
            </div>
            <div class="mt-5 flex h-72 items-end gap-3">
                @foreach($workspace['revenue'] as $value)
                    <div class="flex flex-1 flex-col justify-end">
                        <div class="rounded-t-md bg-brand-500/80" style="height: {{ max(28, $value / 90) }}px"></div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-lg border border-pc-border bg-white p-5 shadow-sm">
            <h2 class="text-base font-semibold text-pc-text">Funil comercial</h2>
            <div class="mt-5 space-y-3">
                @foreach($workspace['funnel'] as $step)
                    <div>
                        <div class="mb-1 flex justify-between text-sm">
                            <span class="font-medium text-pc-text">{{ $step['label'] }}</span>
                            <span class="text-pc-secondary">{{ number_format($step['value'], 0, ',', '.') }}</span>
                        </div>
                        <div class="h-2 rounded-full bg-pc-muted">
                            <div class="h-2 rounded-full bg-brand-500" style="width: {{ min(100, $step['value'] / 28.4) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="grid gap-4 xl:grid-cols-3">
        <div class="rounded-lg border border-pc-border bg-white p-5 shadow-sm xl:col-span-2">
            <h2 class="text-base font-semibold text-pc-text">Widgets operacionais</h2>
            <div class="mt-4 grid gap-3 md:grid-cols-3">
                @foreach(['Pedidos em andamento' => count($workspace['orders']), 'Conversas abertas' => count($workspace['conversations']), 'Eventos de integracao' => count($workspace['integration_events'])] as $label => $value)
                    <div class="rounded-lg border border-pc-border bg-pc-muted p-4">
                        <p class="text-sm text-pc-secondary">{{ $label }}</p>
                        <p class="mt-2 text-2xl font-semibold text-pc-text">{{ $value }}</p>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="rounded-lg border border-pc-border bg-white p-5 shadow-sm">
            <h2 class="text-base font-semibold text-pc-text">Insights do Pedido Certo</h2>
            <div class="mt-4 space-y-3">
                @foreach($workspace['insights'] as $insight)
                    <a href="{{ $insight['route'] }}" class="block rounded-md border border-brand-100 bg-brand-50 px-3 py-2 text-sm text-pc-text">
                        <span class="block font-semibold">{{ $insight['title'] }}</span>
                        <span class="mt-1 block text-pc-secondary">{{ $insight['body'] }}</span>
                        <span class="mt-2 block text-xs font-semibold text-brand-700">{{ $insight['impact'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
</div>
