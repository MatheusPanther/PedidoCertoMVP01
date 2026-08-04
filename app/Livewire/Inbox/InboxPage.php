<?php

namespace App\Livewire\Inbox;

use App\Enums\ConversationStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\Workspace\WorkspaceDataService;
use App\Support\BrazilFormat;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class InboxPage extends Component
{
    public string $filter = 'all';

    public string $search = '';

    public bool $orderModalOpen = false;

    public ?string $selectedConversationId = null;

    public string $messageBody = '';

    public string $orderProductName = 'Combo Smash Duplo';

    public int $orderQuantity = 1;

    public float $orderTotal = 42.90;

    public function assumeConversation(): void
    {
        $conversation = $this->selectedConversation();

        if (! $conversation) {
            $this->dispatch('toast', message: 'Selecione uma conversa para assumir.');

            return;
        }

        $conversation->update([
            'assigned_user_id' => Auth::id(),
            'status' => ConversationStatus::HumanHandling,
            'ai_paused' => true,
        ]);

        $this->dispatch('toast', message: 'Atendimento assumido. A IA foi pausada nesta conversa.');
    }

    public function returnToAi(): void
    {
        $conversation = $this->selectedConversation();

        if (! $conversation) {
            $this->dispatch('toast', message: 'Selecione uma conversa para devolver para a IA.');

            return;
        }

        $conversation->update([
            'assigned_user_id' => null,
            'status' => ConversationStatus::AiHandling,
            'ai_paused' => false,
        ]);

        $this->dispatch('toast', message: 'Atendimento devolvido para a IA.');
    }

    public function selectConversation(string $conversationId): void
    {
        $this->selectedConversationId = $conversationId;
    }

    public function sendMessage(): void
    {
        $this->validate(['messageBody' => ['required', 'string', 'max:1000']]);

        $conversation = $this->selectedConversation();

        if (! $conversation) {
            $this->dispatch('toast', message: 'Selecione uma conversa antes de enviar.');

            return;
        }

        Message::query()->create([
            'organization_id' => $conversation->organization_id,
            'restaurant_id' => $conversation->restaurant_id,
            'branch_id' => $conversation->branch_id,
            'conversation_id' => $conversation->id,
            'customer_id' => $conversation->customer_id,
            'user_id' => Auth::id(),
            'channel' => $conversation->channel,
            'direction' => 'outbound',
            'body' => $this->messageBody,
            'sent_at' => now(),
        ]);

        $conversation->update([
            'last_message_at' => now(),
            'unread_count' => 0,
        ]);

        $this->messageBody = '';
        $this->dispatch('toast', message: 'Mensagem enviada e registrada no histórico.');
    }

    public function openOrderModal(): void
    {
        $conversation = $this->selectedConversation();

        if (! $conversation) {
            $this->dispatch('toast', message: 'Selecione uma conversa para criar o pedido.');

            return;
        }

        $product = app(WorkspaceDataService::class)->productsQuery()->where('available', true)->first();

        if ($product) {
            $this->orderProductName = $product->name;
            $this->orderTotal = (float) $product->price;
        }

        $this->orderModalOpen = true;
    }

    public function createOrderFromConversation(): void
    {
        $this->validate([
            'orderProductName' => ['required', 'string', 'max:160'],
            'orderQuantity' => ['required', 'integer', 'min:1', 'max:20'],
            'orderTotal' => ['required', 'numeric', 'min:1'],
        ]);

        $conversation = $this->selectedConversation();

        if (! $conversation) {
            $this->dispatch('toast', message: 'Conversa indisponível para criar pedido.');

            return;
        }

        $nextNumber = (string) (((int) Order::query()
            ->where('organization_id', $conversation->organization_id)
            ->where('restaurant_id', $conversation->restaurant_id)
            ->max('number')) + 1);

        $order = Order::query()->create([
            'organization_id' => $conversation->organization_id,
            'restaurant_id' => $conversation->restaurant_id,
            'branch_id' => $conversation->branch_id,
            'customer_id' => $conversation->customer_id,
            'conversation_id' => $conversation->getKey(),
            'number' => $nextNumber,
            'channel' => $conversation->channel,
            'status' => OrderStatus::PendingConfirmation,
            'payment_status' => PaymentStatus::Pending,
            'payment_method' => 'Pix',
            'subtotal' => $this->orderTotal,
            'delivery_fee' => 0,
            'discount' => 0,
            'total' => $this->orderTotal,
            'origin' => 'human',
            'estimated_delivery_at' => now()->addMinutes(35),
            'history' => [['status' => OrderStatus::PendingConfirmation->value, 'at' => now()->toISOString()]],
        ]);

        OrderItem::query()->create([
            'organization_id' => $conversation->organization_id,
            'restaurant_id' => $conversation->restaurant_id,
            'branch_id' => $conversation->branch_id,
            'order_id' => $order->id,
            'product_name' => $this->orderProductName,
            'quantity' => $this->orderQuantity,
            'unit_price' => $this->orderTotal / $this->orderQuantity,
            'total' => $this->orderTotal,
        ]);

        $conversation->update([
            'status' => ConversationStatus::OrderStarted,
            'last_message_at' => now(),
        ]);

        $this->orderModalOpen = false;
        $this->dispatch('toast', message: "Pedido #{$order->number} criado a partir da conversa.");
    }

    public function render(WorkspaceDataService $data)
    {
        $query = $data->conversationsQuery()
            ->with('customer')
            ->withCount('messages')
            ->when($this->filter !== 'all', fn ($query) => $query->where('status', $this->filter))
            ->when($this->search !== '', fn ($query) => $query->whereHas('customer', fn ($customerQuery) => $customerQuery
                ->where('name', 'like', "%{$this->search}%")
                ->orWhere('phone', 'like', "%{$this->search}%")))
            ->latest('last_message_at');

        $conversations = $query->get();
        $selected = $this->selectedConversationId
            ? $conversations->firstWhere('id', $this->selectedConversationId)
            : $conversations->first();

        if ($selected && $this->selectedConversationId !== $selected->id) {
            $this->selectedConversationId = $selected->id;
        }

        $messages = $selected
            ? $selected->messages()->latest('sent_at')->limit(30)->get()->reverse()->values()
            : collect();

        return view('livewire.inbox.inbox-page', [
            'conversations' => $conversations,
            'selected' => $selected,
            'messages' => $messages,
            'profile' => $selected?->customer,
            'formattedOrderTotal' => BrazilFormat::money($this->orderTotal),
        ]);
    }

    private function selectedConversation(): ?Conversation
    {
        if (! $this->selectedConversationId) {
            return null;
        }

        return app(WorkspaceDataService::class)->conversationsQuery()
            ->with('customer')
            ->find($this->selectedConversationId);
    }
}
