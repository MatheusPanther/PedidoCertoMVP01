<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\IntegrationsPageController;
use App\Livewire\App\SimplePage;
use App\Livewire\Campaigns\CampaignsPage;
use App\Livewire\Coupons\CouponsPage;
use App\Livewire\Customers\CustomersPage;
use App\Livewire\Dashboard\DashboardPage;
use App\Livewire\Inbox\InboxPage;
use App\Livewire\Menu\MenuPage;
use App\Livewire\Orders\OrdersPage;
use App\Livewire\Remarketing\RemarketingPage;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'tenant'])->prefix('app')->group(function () {
    Route::get('/dashboard', DashboardPage::class)->name('dashboard');
    Route::get('/atendimento', InboxPage::class)->name('inbox');
    Route::get('/pedidos', OrdersPage::class)->name('orders');
    Route::get('/clientes', CustomersPage::class)->name('customers');
    Route::get('/cardapio', MenuPage::class)->name('menu');

    Route::get('/pipeline', fn () => view('app.simple', SimplePage::make('Pipeline', 'Fluxo comercial separado da cozinha, com etapas configuraveis e automacoes por mudanca.')))->name('pipeline');
    Route::get('/cupons', CouponsPage::class)->name('coupons');
    Route::get('/campanhas', CampaignsPage::class)->name('campaigns');
    Route::get('/remarketing', RemarketingPage::class)->name('remarketing');
    Route::get('/fidelidade', fn () => view('app.simple', SimplePage::make('Fidelidade', 'Programa de pontos, niveis Bronze, Prata, Ouro e VIP, recompensas e resgates.')))->name('loyalty');
    Route::get('/relatorios', fn () => view('app.simple', SimplePage::make('Relatorios', 'Relatorios exportaveis de vendas, produtos, canais, IA, atendentes e bairros.')))->name('reports');
    Route::get('/financeiro', fn () => view('app.simple', SimplePage::make('Financeiro', 'Receita bruta, liquida, descontos, taxas, pedidos pagos, pendentes e reembolsos.')))->name('finance');
    Route::get('/automacoes', fn () => view('app.simple', SimplePage::make('Automacoes', 'Fluxos orientados a eventos, filas, condicoes, excecoes e webhooks para o n8n.')))->name('automations');
    Route::get('/integracoes', [IntegrationsPageController::class, 'index'])->name('integrations');
    Route::post('/integracoes/evento-teste', [IntegrationsPageController::class, 'createTestEvent'])->name('integrations.test-event');
    Route::post('/integracoes/{event}/reprocessar', [IntegrationsPageController::class, 'reprocess'])->name('integrations.reprocess');
    Route::get('/configuracoes', fn () => view('app.simple', SimplePage::make('Configuracoes', 'Organizacao, restaurantes, unidades, equipe, permissoes, IA e canais.')))->name('settings');
});
