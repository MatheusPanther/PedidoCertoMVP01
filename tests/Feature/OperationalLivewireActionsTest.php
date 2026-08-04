<?php

namespace Tests\Feature;

use App\Enums\ConversationStatus;
use App\Livewire\Inbox\InboxPage;
use App\Livewire\Menu\MenuPage;
use App\Livewire\Orders\OrdersPage;
use App\Models\Conversation;
use App\Models\Order;
use App\Models\Organization;
use App\Models\Product;
use App\Models\Restaurant;
use App\Models\User;
use App\Services\Tenancy\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OperationalLivewireActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_page_creates_manual_order(): void
    {
        $this->bootDemoTenant();

        Livewire::test(OrdersPage::class)
            ->call('createManualOrder')
            ->assertSet('drawerOpen', true);

        $this->assertSame(2, Order::query()->count());
        $this->assertDatabaseHas('orders', ['origin' => 'manual']);
    }

    public function test_inbox_handoff_message_and_order_creation_are_persisted(): void
    {
        $this->bootDemoTenant();
        $conversation = Conversation::query()->firstOrFail();

        Livewire::test(InboxPage::class)
            ->set('selectedConversationId', $conversation->id)
            ->call('assumeConversation')
            ->set('messageBody', 'Resposta operacional validada.')
            ->call('sendMessage')
            ->set('orderProductName', 'Combo teste')
            ->set('orderQuantity', 1)
            ->set('orderTotal', 49.9)
            ->call('createOrderFromConversation');

        $conversation->refresh();

        $this->assertTrue($conversation->ai_paused);
        $this->assertSame(ConversationStatus::OrderStarted, $conversation->status);
        $this->assertDatabaseHas('messages', ['body' => 'Resposta operacional validada.', 'direction' => 'outbound']);
        $this->assertSame(2, Order::query()->count());
    }

    public function test_menu_toggle_updates_product_availability(): void
    {
        $this->bootDemoTenant();
        $product = Product::query()->firstOrFail();

        Livewire::test(MenuPage::class)
            ->call('toggleProduct', $product->id);

        $this->assertFalse($product->fresh()?->available);
    }

    private function bootDemoTenant(): void
    {
        $this->seed();

        $organization = Organization::query()->firstOrFail();
        $restaurant = Restaurant::query()->firstOrFail();
        $user = User::query()->firstOrFail();

        $this->actingAs($user);
        app()->instance(TenantContext::class, new TenantContext($organization, $restaurant));
    }
}
