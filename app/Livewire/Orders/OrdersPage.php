<?php

namespace App\Livewire\Orders;

use App\Enums\Channel;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\Tenancy\TenantContext;
use App\Services\Workspace\WorkspaceDataService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class OrdersPage extends Component
{
    public string $view = 'kanban';

    public bool $drawerOpen = false;

    public ?string $selectedOrderId = null;

    public function updateStatus(string $orderId, string $status): void
    {
        $order = app(WorkspaceDataService::class)->ordersQuery()->findOrFail($orderId);
        $target = OrderStatus::from($status);
        $history = $order->history ?? [];
        $history[] = ['status' => $target->value, 'at' => now()->toISOString()];

        $order->update([
            'status' => $target,
            'history' => $history,
        ]);

        $this->dispatch('toast', message: "Pedido #{$order->number} movido para {$this->statusLabel($target)}.");
    }

    public function createManualOrder(): void
    {
        $tenant = app(TenantContext::class);
        $customer = app(WorkspaceDataService::class)->customersQuery()->first();

        if (! $tenant->organization || ! $tenant->restaurant || ! $customer) {
            $this->dispatch('toast', message: 'Cadastre organizacao, restaurante e cliente antes de criar pedido manual.');

            return;
        }

        $branchId = $tenant->restaurant->branches()->value('id');
        $nextNumber = (string) (((int) Order::query()
            ->where('organization_id', $tenant->organization->id)
            ->where('restaurant_id', $tenant->restaurant->id)
            ->max('number')) + 1);

        $order = Order::query()->create([
            'organization_id' => $tenant->organization->id,
            'restaurant_id' => $tenant->restaurant->id,
            'branch_id' => $branchId,
            'customer_id' => $customer->id,
            'number' => $nextNumber,
            'channel' => Channel::Manual,
            'status' => OrderStatus::New,
            'payment_status' => PaymentStatus::Pending,
            'payment_method' => 'Pix',
            'subtotal' => 42.90,
            'delivery_fee' => 8,
            'discount' => 0,
            'total' => 50.90,
            'origin' => 'manual',
            'estimated_delivery_at' => now()->addMinutes(40),
            'history' => [['status' => OrderStatus::New->value, 'at' => now()->toISOString()]],
        ]);

        OrderItem::query()->create([
            'organization_id' => $tenant->organization->id,
            'restaurant_id' => $tenant->restaurant->id,
            'branch_id' => $branchId,
            'order_id' => $order->id,
            'product_name' => 'Pedido manual balcão',
            'quantity' => 1,
            'unit_price' => 42.90,
            'total' => 42.90,
        ]);

        $this->selectedOrderId = $order->id;
        $this->drawerOpen = true;
        $this->dispatch('toast', message: "Pedido #{$order->number} criado.");
    }

    public function openDrawer(string $orderId): void
    {
        $this->selectedOrderId = $orderId;
        $this->drawerOpen = true;
    }

    public function closeDrawer(): void
    {
        $this->drawerOpen = false;
    }

    public function render(WorkspaceDataService $data)
    {
        $orders = $data->ordersQuery()->with(['customer', 'items'])->latest()->get();
        $selectedOrder = $this->selectedOrderId
            ? $orders->firstWhere('id', $this->selectedOrderId)
            : $orders->first();

        return view('livewire.orders.orders-page', [
            'orders' => $orders,
            'selectedOrder' => $selectedOrder,
            'statuses' => [
                OrderStatus::New->value => 'Novo',
                OrderStatus::Confirmed->value => 'Confirmado',
                OrderStatus::Preparing->value => 'Em preparo',
                OrderStatus::OutForDelivery->value => 'Saiu para entrega',
            ],
        ]);
    }

    private function statusLabel(OrderStatus $status): string
    {
        return str($status->value)->replace('_', ' ')->title()->toString();
    }
}
