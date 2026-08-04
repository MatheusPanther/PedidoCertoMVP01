<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pedido Certo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-pc-background text-pc-text">
    <main class="grid min-h-screen place-items-center px-6">
        <section class="w-full max-w-xl rounded-lg border border-pc-border bg-white p-8 shadow-sm">
            <x-logo />
            <h1 class="mt-6 text-2xl font-semibold">Pedido Certo CRM</h1>
            <p class="mt-2 text-sm text-pc-secondary">Operacao SaaS para atendimento, pedidos, cardapio, campanhas e automacoes de restaurantes.</p>
            <a href="{{ route('login') }}" class="mt-6 inline-flex rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white">Acessar sistema</a>
        </section>
    </main>
</body>
</html>
