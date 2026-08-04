<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Entrar - Pedido Certo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-pc-background font-sans text-pc-text">
    <main class="grid min-h-screen place-items-center px-4">
        <section class="w-full max-w-md rounded-lg border border-pc-border bg-white p-6 shadow-sm">
            <x-logo />
            <div class="mt-8">
                <h1 class="text-2xl font-semibold">Entrar no Pedido Certo</h1>
                <p class="mt-2 text-sm text-pc-secondary">Acesse a operacao comercial e de atendimento do restaurante.</p>
            </div>
            <form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label class="text-sm font-medium text-pc-text" for="email">E-mail</label>
                    <input id="email" name="email" type="email" value="{{ old('email', 'admin@pedidocerto.test') }}" class="mt-1 w-full rounded-md border border-pc-border px-3 py-2 text-sm" required autofocus>
                    @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-pc-text" for="password">Senha</label>
                    <input id="password" name="password" type="password" value="password" class="mt-1 w-full rounded-md border border-pc-border px-3 py-2 text-sm" required>
                </div>
                <label class="flex items-center gap-2 text-sm text-pc-secondary">
                    <input type="checkbox" name="remember" class="rounded border-pc-border text-brand-500">
                    Manter sessao ativa
                </label>
                <button class="w-full rounded-md bg-brand-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-600">Entrar</button>
            </form>
        </section>
    </main>
</body>
</html>
