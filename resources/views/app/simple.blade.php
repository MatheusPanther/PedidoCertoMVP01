<x-layouts.app :title="$title">
    @php
        $modules = [
            'Pipeline' => [
                ['Conversas qualificadas', 'atendimento', route('inbox')],
                ['Pedidos em negociacao', 'pedidos', route('orders')],
                ['Campanhas de conversao', 'campanhas', route('campaigns')],
            ],
            'Fidelidade' => [
                ['Clientes VIP', 'clientes', route('customers')],
                ['Cupons de recompensa', 'cupons', route('coupons')],
                ['Campanhas recorrentes', 'campanhas', route('campaigns')],
            ],
            'Relatorios' => [
                ['Receita por canal', 'financeiro', route('finance')],
                ['Pedidos por status', 'pedidos', route('orders')],
                ['Atendimento e IA', 'atendimento', route('inbox')],
            ],
            'Financeiro' => [
                ['Pedidos pagos', 'pedidos', route('orders')],
                ['Receita de campanhas', 'campanhas', route('campaigns')],
                ['Cupons e descontos', 'cupons', route('coupons')],
            ],
            'Automacoes' => [
                ['Remarketing ativo', 'remarketing', route('remarketing')],
                ['Eventos n8n', 'integracoes', route('integrations')],
                ['Campanhas agendadas', 'campanhas', route('campaigns')],
            ],
            'Configuracoes' => [
                ['Integracoes', 'n8n e webhooks', route('integrations')],
                ['Cardapio', 'produtos e categorias', route('menu')],
                ['Equipe operacional', 'permissoes por funcao', route('dashboard')],
            ],
        ];

        $items = $modules[$title] ?? [];
    @endphp

    <div class="space-y-5">
        <div>
            <h1 class="text-2xl font-semibold text-pc-text">{{ $title }}</h1>
            <p class="mt-1 max-w-3xl text-sm text-pc-secondary">{{ $description }}</p>
        </div>
        <section class="grid gap-4 lg:grid-cols-3">
            @foreach($items as [$heading, $meta, $href])
                <article class="rounded-lg border border-pc-border bg-white p-5 shadow-sm">
                    <h2 class="font-semibold text-pc-text">{{ $heading }}</h2>
                    <p class="mt-2 text-sm text-pc-secondary">{{ ucfirst($meta) }}</p>
                    <a href="{{ $href }}" class="mt-4 inline-flex rounded-md bg-brand-50 px-3 py-2 text-sm font-semibold text-brand-700">Abrir</a>
                </article>
            @endforeach
        </section>
        <section class="rounded-lg border border-pc-border bg-white p-5 shadow-sm">
            <h2 class="font-semibold text-pc-text">Operacao desta area</h2>
            <div class="mt-4 grid gap-3 md:grid-cols-3">
                <div class="rounded-lg bg-pc-muted p-4">
                    <p class="text-sm font-medium text-pc-text">Dados conectados</p>
                    <p class="mt-1 text-sm text-pc-secondary">Os atalhos usam clientes, pedidos, campanhas, automacoes e eventos gravados no banco local.</p>
                </div>
                <div class="rounded-lg bg-pc-muted p-4">
                    <p class="text-sm font-medium text-pc-text">Exportacao</p>
                    <p class="mt-1 text-sm text-pc-secondary">Pendente de storage externo configurado; use relatorios internos ate conectar credenciais.</p>
                </div>
                <div class="rounded-lg bg-pc-muted p-4">
                    <p class="text-sm font-medium text-pc-text">Permissoes</p>
                    <p class="mt-1 text-sm text-pc-secondary">Acesso protegido por autenticacao e contexto da organizacao ativa.</p>
                </div>
            </div>
        </section>
    </div>
</x-layouts.app>
