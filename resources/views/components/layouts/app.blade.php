@props(['title' => 'Pedido Certo'])

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} - Pedido Certo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased">
    @php
        $sections = [
            'Visao geral' => [['Dashboard', 'dashboard']],
            'Operacao' => [['Atendimento', 'inbox'], ['Pedidos', 'orders'], ['Pipeline', 'pipeline'], ['Clientes', 'customers'], ['Cardapio', 'menu']],
            'Crescimento' => [['Cupons', 'coupons'], ['Campanhas', 'campaigns'], ['Remarketing', 'remarketing'], ['Fidelidade', 'loyalty']],
            'Inteligencia' => [['Relatorios', 'reports'], ['Financeiro', 'finance']],
            'Administracao' => [['Automacoes', 'automations'], ['Integracoes', 'integrations'], ['Configuracoes', 'settings']],
        ];
    @endphp

    <div x-data="{ collapsed: false, command: false, toast: '', showToast: false }"
         x-on:keydown.window.meta.k.prevent="command = true"
         x-on:toast.window="toast = $event.detail.message; showToast = true; setTimeout(() => showToast = false, 2800)"
         class="min-h-screen bg-pc-background">
        <aside class="fixed inset-y-0 left-0 z-30 hidden border-r border-pc-border bg-white lg:flex lg:flex-col"
               :class="collapsed ? 'w-20' : 'w-72'">
            <div class="flex h-16 items-center justify-between border-b border-pc-border px-4">
                <div x-show="!collapsed"><x-logo /></div>
                <div x-show="collapsed"><x-logo compact /></div>
                <button type="button" class="grid size-9 place-items-center rounded-md border border-pc-border text-pc-secondary hover:bg-pc-muted" x-on:click="collapsed = !collapsed" title="Recolher menu">
                    <span class="text-lg">‹</span>
                </button>
            </div>
            <div class="px-4 py-4" x-show="!collapsed">
                <p class="text-xs font-medium uppercase text-pc-secondary">Restaurante selecionado</p>
                <p class="mt-1 text-sm font-semibold text-pc-text">Pedido Certo Burger</p>
                <p class="text-xs text-pc-secondary">Unidade Atalaia</p>
            </div>
            <nav class="flex-1 space-y-5 overflow-y-auto px-3 py-4">
                @foreach($sections as $section => $items)
                    <div>
                        <p class="px-3 text-xs font-semibold uppercase text-pc-secondary" x-show="!collapsed">{{ $section }}</p>
                        <div class="mt-2 space-y-1">
                            @foreach($items as [$label, $route])
                                <a href="{{ route($route) }}" title="{{ $label }}"
                                   class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs($route) ? 'bg-brand-50 text-brand-700' : 'text-pc-secondary hover:bg-pc-muted hover:text-pc-text' }}">
                                    <span class="size-2 rounded-full {{ request()->routeIs($route) ? 'bg-brand-500' : 'bg-pc-border' }}"></span>
                                    <span x-show="!collapsed">{{ $label }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </nav>
            <div class="border-t border-pc-border p-3">
                <button type="button" x-on:click="$dispatch('toast', { message: 'Solicitacao de suporte registrada para a Ampliize.' })" class="w-full rounded-md border border-pc-border px-3 py-2 text-sm font-medium text-pc-secondary hover:bg-pc-muted" x-show="!collapsed">Suporte Ampliize</button>
            </div>
        </aside>

        <main class="lg:pl-72" :class="collapsed ? 'lg:pl-20' : 'lg:pl-72'">
            <header class="sticky top-0 z-20 border-b border-pc-border bg-white/95 backdrop-blur">
                <div class="flex h-16 items-center gap-3 px-4 lg:px-8">
                    <div class="lg:hidden"><x-logo compact /></div>
                    <div class="hidden text-sm text-pc-secondary md:block">Pedido Certo / <span class="font-medium text-pc-text">{{ $title }}</span></div>
                    <button x-on:click="command = true" class="ml-auto hidden min-w-72 items-center justify-between rounded-md border border-pc-border bg-pc-muted px-3 py-2 text-sm text-pc-secondary md:flex">
                        Buscar clientes, pedidos, conversas
                        <span class="rounded border border-pc-border bg-white px-1.5 text-xs">⌘K</span>
                    </button>
                    <select class="rounded-md border border-pc-border bg-white px-3 py-2 text-sm text-pc-text">
                        <option>Unidade Atalaia</option>
                        <option>Unidade Centro</option>
                    </select>
                    <select class="rounded-md border border-pc-border bg-white px-3 py-2 text-sm text-pc-text">
                        <option>Ultimos 30 dias</option>
                        <option>Hoje</option>
                        <option>Ultimos 7 dias</option>
                    </select>
                    <button type="button" x-on:click="$dispatch('toast', { message: '14 conversas aguardam atendimento humano.' })" class="grid size-9 place-items-center rounded-md border border-pc-border text-pc-secondary hover:bg-pc-muted" title="Notificacoes">!</button>
                    <div class="grid size-9 place-items-center rounded-full bg-pc-text text-sm font-semibold text-white">MA</div>
                </div>
            </header>

            <section class="px-4 py-6 lg:px-8">
                {{ $slot }}
            </section>
        </main>

        <div x-cloak x-show="command" class="fixed inset-0 z-50 grid place-items-start bg-black/20 p-4 pt-24" x-on:click.self="command = false">
            <div class="mx-auto w-full max-w-xl rounded-lg border border-pc-border bg-white p-3 shadow-xl">
                <input class="w-full rounded-md border border-pc-border px-3 py-3 text-sm outline-none focus:border-brand-300" placeholder="Digite um pedido, cliente, conversa ou acao">
                <div class="mt-3 divide-y divide-pc-border text-sm">
                    <a href="{{ route('orders') }}" class="block w-full px-3 py-3 text-left hover:bg-pc-muted">Criar pedido manual</a>
                    <a href="{{ route('coupons') }}" class="block w-full px-3 py-3 text-left hover:bg-pc-muted">Enviar cupom de reativacao</a>
                    <a href="{{ route('inbox') }}" class="block w-full px-3 py-3 text-left hover:bg-pc-muted">Abrir conversas aguardando humano</a>
                </div>
            </div>
        </div>

        <div x-cloak x-show="showToast" class="fixed bottom-5 right-5 z-50 rounded-lg border border-pc-border bg-white px-4 py-3 text-sm font-medium text-pc-text shadow-lg" x-text="toast"></div>
    </div>
    @livewireScripts
</body>
</html>
